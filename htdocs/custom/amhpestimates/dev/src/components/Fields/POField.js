import React from "react"
import { computed } from 'mobx';
import { observer } from 'mobx-react'

@observer
export default class POField extends React.Component {
  onChange(e)
  {
    var owner = this.getOwner();
    owner.setProperty(this.props.prop, e.target.value);
  }

  onBeginChange(e)
  {
    console.log("onBeginChange("+e.target.value+")");
    this.changing = true;
    this.onChange(e);
  }

  onEndChange(e)
  {
    console.log("onEndChange("+e.target.value+")");
    this.changing = false;
    this.onChange(e);
  }

  // The owner is either a Production Order or a Produciton Order Item
  getOwner()
  {
    // Read both properties and then choose the one that is not empty
    var po = this.props.po;
    var item = this.props.item;
    return po ? po : item;
  }

  @computed get isReadOnly() { return this.props.readOnly == "true" ||  this.props.readOnly == true; }
}