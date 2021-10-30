import React from "react";

class Pill extends React.Component {

  render() {
    // console.log(this.props);

    return (
      <div className="component-pill me-2">
        <div className="component-pill-text-left">{this.props.data.name}</div>
        <button className="component-pill-button">
          <div className="component-pill-text-right" onClick={this.props.handleClick}>
            <i className="fa fa-times" aria-hidden="true"></i>
          </div>
        </button>
      </div>
    )
  }
}

export default Pill;