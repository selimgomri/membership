import React from "react";
import Pill from "./Pill";

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
            <button className="btn btn-primary">
              To
            </button>
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