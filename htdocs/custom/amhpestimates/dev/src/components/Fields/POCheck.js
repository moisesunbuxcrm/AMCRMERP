import React from "react"
import { observer } from 'mobx-react'
import POField from './POField'

@observer
export default class POCheck extends POField {
  render() {
    var owner = this.getOwner();
    var prop = this.props.prop;
    var val = owner[prop];
    var checked = (val == 1 || val == "1" || val == true || val == "true");
    var readOnly = this.props.readOnly;

    if (readOnly)
    {
      return (
        <input type="checkbox" checked={checked} readOnly />
      )
    }
    else
    {
      return (
        <input type="checkbox" checked={checked} onChange={this.onChange.bind(this)} />
      )
    }
  }

  onChange(e)
  {
    var owner = this.getOwner();
    owner.setProperty(this.props.prop, e.target.checked);
  }
}