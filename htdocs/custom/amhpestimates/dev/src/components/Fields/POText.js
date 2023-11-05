import React from "react"
import { observer } from 'mobx-react'
import POField from './POField'

@observer
export default class POText extends POField {
  render() {
    var owner = this.getOwner();
    var prop = this.props.prop;
    var val = owner[prop];
    var linkTo = this.props.linkTo;

    if (val == null)
      val = "";

    if (!this.isReadOnly)
    {
      var className="text100";
      var size=this.props.size>0?this.props.size:"";

      if (size!="")
        className = "";

      return (
        <input type="text" className={className} size={size} value={val} onChange={this.onChange.bind(this)} />
      )
    }
    else{
      if (linkTo)
        return <a href={linkTo}>{val}</a>;
      return (<span>{val}</span>);
    }
  }
}