import React from "react";
import ReactDOM from "react-dom";
import Header from '../components/Header';
import Breadcrumb from 'react-bootstrap/Breadcrumb';
import ToRow from '../components/ToRow';
import DropdownRow from "../components/DropdownRow";
import TextRow from "../components/TextRow";

class App extends React.Component {

  constructor() {
    super();
    this.state = {
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
      category: 'DEFAULT',

      // Options for this
      possibleTos: [],
      possibleFroms: [],
      possibleReplyTos: [],
      possibleCategories: []
    }
  }

  componentDidMount() {
    fetch('https://testclub.mt.myswimmingclub.uk/notify/new/react-data')
      .then(response => response.json())
      .then(data => {

        // console.info(data);

        this.setState({
          possibleTos: data.possibleTos,
          possibleReplyTos: data.possibleReplyTos,
          possibleFroms: data.possibleFroms,
          canForceSend: data.canForceSend,
          possibleCategories: data.possibleCategories,
        })
      })
  }

  handlePillClick = (event) => {
    console.log(event);
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

  render() {
    const breadcrumbs = (
      <Breadcrumb>
        <Breadcrumb.Item href="/notify">Notify</Breadcrumb.Item>
        <Breadcrumb.Item active>Composer</Breadcrumb.Item>
      </Breadcrumb>
    );

    return (
      <div>
        <Header title="Send a new email" subtitle="Send emails to targeted groups" breadcrumbs={breadcrumbs} />

        <form className="container-xl" /*onChange={this.handleChange}*/>
          <ToRow to={this.state.to} handleClick={this.handlePillClick} />
          <DropdownRow name="from" options={this.state.possibleFroms} label="Send as" formValue={this.state.from} handleChange={this.handleChange} />
          <DropdownRow name="replyTo" options={this.state.possibleReplyTos} label="Send replies to" formValue={this.state.replyTo} handleChange={this.handleChange} />
          <DropdownRow name="category" options={this.state.possibleCategories} label="Subscription category" formValue={this.state.category} handleChange={this.handleChange} />
          <TextRow name="subject" label="Subject" formValue={this.state.subject} handleChange={this.handleChange} />
        </form>
      </div>
    )
  }
}

ReactDOM.render(<App />, document.getElementById('scds-react-root'));
