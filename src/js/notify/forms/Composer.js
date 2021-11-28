import React, { useRef } from "react";
import ReactDOM from "react-dom";
import Header from '../components/Header';
import Breadcrumb from 'react-bootstrap/Breadcrumb';
import ToRow from '../components/ToRow';
import DropdownRow from "../components/DropdownRow";
import TextRow from "../components/TextRow";
import Modal from "react-bootstrap/Modal";
import Button from "react-bootstrap/Button";
import Tabs from "react-bootstrap/Tabs";
import Tab from "react-bootstrap/Tab";
import Form from "react-bootstrap/Form";
import Placeholder from "react-bootstrap/Placeholder";
import { Editor } from "@tinymce/tinymce-react";
import axios from "axios";
import Dropzone from "../components/Dropzone";
import Accordion from "react-bootstrap/Accordion"
// import exports from "enhanced-resolve";

export class Composer extends React.Component {

  constructor() {
    super();
    this.editor = React.createRef();
    this.state = {
      loaded: false,

      from: 'asClub',
      to: [
        {
          name: 'Test',
          type: 'squad',
          id: 20,
        }
      ],
      replyTo: 'toClub',
      subject: "",
      attachments: [],
      message: "",
      forceSend: false,
      canForceSend: false,
      sendToCoaches: true,
      category: 'DEFAULT',

      // Modals
      showTo: false,

      // Options for this
      possibleTos: [],
      possibleFroms: [],
      possibleReplyTos: [],
      possibleCategories: [],

      // TinyMCE
      documentBaseUrl: null,
      imagesUploadUrl: null,
      tinyMceCssLocation: null,
      editorValue: '',

      // Tabs
      tabKey: 'editor',

      // Dropzone
      emailUuid: null,
      date: null,
      dzMaxFileSize: 10485760,
      dzMaxTotalFileSize: 10485760,
      canSubmitAttachments: true,
    }
  }

  componentDidMount() {
    axios.get('/notify/new/react-data')
      .then(response => {
        let data = response.data;
        this.setState({
          possibleTos: data.possibleTos,
          possibleReplyTos: data.possibleReplyTos,
          possibleFroms: data.possibleFroms,
          canForceSend: data.canForceSend,
          possibleCategories: data.possibleCategories,
          documentBaseUrl: data.documentBaseUrl,
          imagesUploadUrl: data.imagesUploadUrl,
          tinyMceCssLocation: data.tinyMceCssLocation,
          date: data.date,
          emailUuid: data.uuid,
          loaded: true,
        })
      })
      .catch(function (error) {
        console.log(error);
      })
  }

  handlePillClick = (event, data) => {
    this.handleTosChangeBackend(data.type, data.key, false);
  }

  handleChange = (event) => {
    // Update state
    const { name, value, type, checked } = event.target;
    if (type === 'checkbox') {
      this.setState({
        [name]: checked,
      });
    } else {
      this.setState({
        [name]: value,
      });
    }
  }

  handleShowTo = (event) => {
    this.setState({
      showTo: true,
    });
  }

  handleCloseTo = (event) => {
    this.setState({
      showTo: false,
    });
  }

  handleSubmit = (event) => {
    event.preventDefault();
    console.log(event);
  }

  setAttachments = (attachments) => {
    this.setState({
      attachments
    });
  }

  setCanSubmitAttachments = (state) => {
    this.setState({
      canSubmitAttachments: state,
    });
  }

  handleTosChange = (event, type) => {
    const { name, checked } = event.target;

    this.handleTosChangeBackend(type, name, checked);

    // const groups = [...this.state.possibleTos[type].groups];
    // this.setState(prevState => ({
    //   ...prevState,
    //   possibleTos: {
    //     ...prevState.possibleTos,
    //     [type]: {
    //       ...prevState.possibleTos[type],
    //       groups: groups.map((data, idx) => {
    //         if (name === data.key) {
    //           return {
    //             ...data,
    //             state: checked,
    //           }
    //         } else {
    //           return { ...data };
    //         }
    //       }),
    //     },
    //   },
    // })
    // )
  }

  handleTosChangeBackend = (type, name, checked) => {
    const groups = [...this.state.possibleTos[type].groups];
    this.setState(prevState => ({
      ...prevState,
      possibleTos: {
        ...prevState.possibleTos,
        [type]: {
          ...prevState.possibleTos[type],
          groups: groups.map((data, idx) => {
            if (name === data.key) {
              return {
                ...data,
                state: checked,
              }
            } else {
              return { ...data };
            }
          }),
        },
      },
    })
    );
  }

  handleSquadTosChange = (event) => {
    this.handleTosChange(event, 'squads');
  }

  handleListTosChange = (event) => {
    this.handleTosChange(event, 'lists');
  }

  handleGalaTosChange = (event) => {
    this.handleTosChange(event, 'galas');
  }

  render() {
    const breadcrumbs = (
      <Breadcrumb>
        <Breadcrumb.Item href="/notify">Notify</Breadcrumb.Item>
        <Breadcrumb.Item active>Composer</Breadcrumb.Item>
      </Breadcrumb>
    );

    const attachments = this.state.attachments.map((data, idx) => {
      return (
        <li key={data.s3_key}><a href={'/files/' + data.url} target="_blank">{data.filename}</a></li>
      )
    })

    let squads, targetedLists, galaEntrants;
    if (this.state.loaded) {
      squads = this.state.possibleTos.squads.groups.map(data => {
        return (
          <Form.Check
            type="checkbox"
            id={data.key}
            key={data.key}
            label={data.name}
            checked={data.state}
            onChange={this.handleSquadTosChange}
            name={data.key}
          />
        )
      });

      targetedLists = this.state.possibleTos.lists.groups.map(data => {
        return (
          <Form.Check
            type="checkbox"
            id={data.key}
            key={data.key}
            label={data.name}
            checked={data.state}
            onChange={this.handleListTosChange}
            name={data.key}
          />
        )
      });

      galaEntrants = this.state.possibleTos.galas.groups.map(data => {
        return (
          <Form.Check
            type="checkbox"
            id={data.key}
            key={data.key}
            label={data.name}
            checked={data.state}
            onChange={this.handleGalaTosChange}
            name={data.key}
          />
        )
      });
    }

    return (
      <div>
        <Header title="Send a new email" subtitle="Send emails to targeted groups" breadcrumbs={breadcrumbs} />

        {
          !this.state.loaded &&
          <>
            <Placeholder xs={6} animation="glow" />
            <Placeholder className="w-75" animation="glow" /> <Placeholder className="w-25" animation="glow" />
          </>
        }

        {
          this.state.loaded &&

          <form className="container-xl" onSubmit={this.handleSubmit} /*onChange={this.handleChange}*/>
            <ToRow to={this.state.possibleTos} handleClick={this.handlePillClick} handleShowTo={this.handleShowTo} />
            <DropdownRow name="from" options={this.state.possibleFroms} label="Send as" formValue={this.state.from} handleChange={this.handleChange} />
            <DropdownRow name="replyTo" options={this.state.possibleReplyTos} label="Send replies to" formValue={this.state.replyTo} handleChange={this.handleChange} />
            <DropdownRow name="category" options={this.state.possibleCategories} label="Subscription category" formValue={this.state.category} handleChange={this.handleChange} />
            <TextRow name="subject" label="Subject" formValue={this.state.subject} handleChange={this.handleChange} />
            <Tabs id="tabs" activeKey={this.state.tabKey} onSelect={(k) => this.setState({ tabKey: k })} className="mb-3">
              <Tab eventKey="editor" title="Editor">
                <div className="mb-3">
                  <Editor
                    tinymceScriptSrc="/js/tinymce/5/tinymce.min.js"
                    onInit={(evt, editor) => this.editor.current = editor}
                    onEditorChange={(value, editor) => { this.setState({ editorValue: value }) }}
                    // initialValue={this.state.editorValue}
                    init={{
                      skin: (window.matchMedia("(prefers-color-scheme: dark)").matches ? "oxide-dark" : ""),
                      relative_urls: false,
                      remove_script_host: false,
                      document_base_url: document.documentURI,
                      selector: '#message',
                      images_upload_url: '/notify/new/image-upload',
                      automatic_uploads: true,
                      images_upload_credentials: true,
                      branding: false,
                      plugins: [
                        'autolink lists link image charmap print preview anchor',
                        'searchreplace visualblocks code autoresize insertdatetime media table',
                        'paste help wordcount'
                      ],
                      statusbar: false,
                      paste_as_text: true,
                      toolbar: 'insert | undo redo |  formatselect | bold italic | bullist numlist outdent indent | removeformat | help',
                      content_css: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default'),
                      fontsize_formats: '12pt',
                      font_formats: 'Default=system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans","Liberation Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";',
                      style_formats: [
                        {
                          title: 'Headings', items: [
                            { title: 'Heading 1', format: 'h1' },
                            { title: 'Heading 2', format: 'h2' },
                            { title: 'Heading 3', format: 'h3' },
                            { title: 'Heading 4', format: 'h4' },
                            { title: 'Heading 5', format: 'h5' },
                            { title: 'Heading 6', format: 'h6' }
                          ]
                        },
                        {
                          title: 'Inline', items: [
                            { title: 'Bold', format: 'bold' },
                            { title: 'Italic', format: 'italic' },
                            { title: 'Underline', format: 'underline' },
                            { title: 'Strikethrough', format: 'strikethrough' },
                            { title: 'Superscript', format: 'superscript' },
                            { title: 'Subscript', format: 'subscript' },
                            { title: 'Code', format: 'code' }
                          ]
                        },
                        {
                          title: 'Blocks', items: [
                            { title: 'Paragraph', format: 'p' },
                            { title: 'Blockquote', format: 'blockquote' },
                            { title: 'Div', format: 'div' },
                            { title: 'Pre', format: 'pre' }
                          ]
                        }
                      ],
                      menu: {
                        edit: { title: 'Edit', items: 'undo redo | cut copy paste | selectall | searchreplace' },
                        view: { title: 'View', items: 'code | visualaid visualchars visualblocks | spellchecker | preview fullscreen' },
                        insert: { title: 'Insert', items: 'image link template codesample inserttable | charmap emoticons hr | pagebreak nonbreaking anchor toc | insertdatetime' },
                        format: { title: 'Format', items: 'bold italic underline strikethrough superscript subscript codeformat | formats blockformats | removeformat' },
                        tools: { title: 'Tools', items: 'spellchecker spellcheckerlanguage | code wordcount' },
                        table: { title: 'Table', items: 'inserttable | cell row column | tableprops deletetable' },
                        help: { title: 'Help', items: 'help' }
                      },

                      //toolbar: "link",
                    }}
                  />
                </div>

                <p>
                  <Button variant="primary" onClick={() => { this.setState({ tabKey: 'preview' }) }}>
                    Preview email content
                  </Button>
                </p>
              </Tab>
              <Tab eventKey="preview" title="Preview">
                <div className="mb-3">
                  <p>This is a preview of your email.</p>

                  {
                    this.state.attachments.length > 0 &&
                    <div className="card mb-3">
                      <div className="card-header">
                        Attachments
                      </div>
                      <div className="card-body">
                        <ul className="list-unstyled mb-0">
                          {attachments}
                        </ul>
                      </div>
                    </div>
                  }

                  <div className="card">
                    <div className="card-header">
                      Content
                    </div>
                    <div className="card-body">
                      <p>Hello -USER-NAME-,</p>
                      <div dangerouslySetInnerHTML={{ __html: this.state.editorValue }} />
                    </div>
                  </div>
                </div>
              </Tab>
              <Tab eventKey="attachments" title="Attachments">
                <Dropzone action="/notify/file-uploads" uuid={this.state.emailUuid} date={this.state.date} maxTotalFileSize={this.state.dzMaxTotalFileSize} maxFileSize={this.state.dzMaxFileSize} setAttachments={this.setAttachments} attachments={this.state.attachments} canSubmitAttachments={this.state.canSubmitAttachments} setCanSubmitAttachments={this.setCanSubmitAttachments} />
              </Tab>
              <Tab eventKey="advanced" title="Advanced">
                <div className="mb-3">
                  {this.state.canForceSend &&
                    <Form.Check
                      type="checkbox"
                      id="force-send"
                      label="Force send this email"
                      checked={this.state.forceSend}
                      onChange={this.handleChange}
                      name="forceSend"
                    />
                  }

                  <Form.Check
                    type="checkbox"
                    id="send-to-coaches"
                    label="Send to coaches of included squads"
                    checked={this.state.sendToCoaches}
                    onChange={this.handleChange}
                    name="sendToCoaches"
                  />
                </div>
              </Tab>
            </Tabs>

            <Modal show={this.state.showTo} onHide={this.handleCloseTo} centered fullscreen>
              <Modal.Header closeButton>
                <Modal.Title>Add recipients</Modal.Title>
              </Modal.Header>
              <Modal.Body className="p-0">
                <Accordion defaultActiveKey="0" flush>
                  {
                    squads.length > 0 &&
                    <Accordion.Item eventKey="0">
                      <Accordion.Header>Squads</Accordion.Header>
                      <Accordion.Body>
                        {squads}
                      </Accordion.Body>
                    </Accordion.Item>
                  }
                  {
                    targetedLists.length > 0 &&
                    <Accordion.Item eventKey="1">
                      <Accordion.Header>Targeted Lists</Accordion.Header>
                      <Accordion.Body>
                        {targetedLists}
                      </Accordion.Body>
                    </Accordion.Item>
                  }
                  {
                    galaEntrants.length > 0 &&
                    <Accordion.Item eventKey="2">
                      <Accordion.Header>Gala Entrants</Accordion.Header>
                      <Accordion.Body>
                        {galaEntrants}
                      </Accordion.Body>
                    </Accordion.Item>
                  }
                </Accordion>
              </Modal.Body>
              <Modal.Footer>
                <Button variant="primary" onClick={this.handleCloseTo}>
                  Save and close
                </Button>
              </Modal.Footer>
            </Modal>
          </form>
        }
      </div>
    )
  }
}

// ReactDOM.render(<App />, document.getElementById('scds-react-root'));
export default Composer;
