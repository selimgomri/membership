import React from "react";
import ReactDOM from "react-dom";
import Header from '../components/Header';
import Breadcrumb from 'react-bootstrap/Breadcrumb';

class App extends React.Component {
  render() {
    const breadcrumbs = (
      <Breadcrumb>
        <Breadcrumb.Item href="/">Notify</Breadcrumb.Item>
        <Breadcrumb.Item active>Composer</Breadcrumb.Item>
      </Breadcrumb>
    );

    return (
      <div>
        <Header title="Notify Composer" subtitle="Send emails to targeted groups" breadcrumbs={breadcrumbs} />
      </div>
    )
  }
}

ReactDOM.render(<App />, document.getElementById('scds-react-root'));
