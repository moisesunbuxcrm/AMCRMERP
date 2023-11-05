import React from "react"
import { observer } from 'mobx-react'
import POField from './POField'

@observer
export default class POTextArea extends POField {
  render() {
    var owner = this.getOwner();
    var prop = this.props.prop;
    var val = owner[prop];
    var readOnly = this.props.readOnly;

    if (val == null)
      val = "";

    var className="text100";
    var rows=this.props.rows>0?this.props.rows:4;
    var cols=this.props.cols>0?this.props.cols:"";

    if (cols!="")
      className = "";

      if (readOnly)
        return (
          <textarea className={className} rows={rows} cols={cols} readOnly value={val} />
        )
      else
        return (
        <textarea className={className} rows={rows} cols={cols} onChange={this.onChange.bind(this)} value={val} />
      )
  }
}