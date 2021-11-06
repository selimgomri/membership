import React from "react";
import Pill from "./Pill";
import Button from "react-bootstrap/Button";

class ToRow extends React.Component {

  render() {
    const pills = this.props.to.map((data, idx) => {
      return (
        <Pill key={idx} data={data} handleClick={this.props.handleClick} />
      )
    })

    return (
      <div className="my-2 border rounded p-2">
        <div className="row">
          <div className="col-auto">
            <Button variant="primary" onClick={this.props.handleShowTo}>
              To
            </Button>
          </div>
          <div className="col p-1">
            {pills}
          </div>
        </div>
      </div>
    )
  }
}

export default ToRow;