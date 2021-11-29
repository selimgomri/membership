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
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import { Link } from "react-router-dom";
// import exports from "enhanced-resolve";

export class Success extends React.Component {

  constructor() {
    super();
  }

  render() {
    const breadcrumbs = (
      <Breadcrumb>
        <Breadcrumb.Item href="/notify">Notify</Breadcrumb.Item>
        <Breadcrumb.Item active>Composer</Breadcrumb.Item>
      </Breadcrumb>
    );

    return (
      <>
        <Header title="Send a new email" subtitle="Send emails to targeted groups" breadcrumbs={breadcrumbs} />

        <div className="container-xl">
          <div className="alert alert-success">
            <p className="mb-0">
              <strong>We have successfully sent your email</strong>
            </p>
            <p className="mb-0">
              Thank you for trying the new Notify Composer. We welcome any feedback.
            </p>
          </div>
        </div>

      </>
    );
  }

}

// ReactDOM.render(<App />, document.getElementById('scds-react-root'));
export default Success;
