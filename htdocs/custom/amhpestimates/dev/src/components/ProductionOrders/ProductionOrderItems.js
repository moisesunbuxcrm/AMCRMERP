import React from "react"
import { observer } from 'mobx-react'
import ProductionOrderItem from "./ProductionOrderItem"

@observer
export default class ProductionOrderItems extends React.Component {
  render() {
    var po = this.props.po;
    var items = po.Items;
		var readOnly = this.props.readOnly;
    var itemComponents = items.map((item) => <ProductionOrderItem key={item.PODescriptionID} item={item} readOnly={readOnly} />);
    var isPanels = po.TYPE == "PANELS";
    var hideIfPanels = isPanels ? { display: "none" } : null;
    var hideIfReadOnly = readOnly ? { display: "none" } : null;

    return (
      <div>
        <form>
          <div className="div-table-responsive">
            <table className="itemstable noborder" width="100%">
              <tbody>
                <tr>
                  <td className="itemsTitle" rowSpan="2">#</td>
                  <td className="itemsTitle" rowSpan="2">PRODUCT</td>
                  <td className="itemsTitle" rowSpan="2">MATERIAL</td>
                  <td className="itemsTitle" rowSpan="2">WINDOW<br/>TYPE</td>
                  <td className="itemsTitle" rowSpan="2">COLOR</td>
                  <td className="itemsTitle" rowSpan="2">PROVIDER</td>
                  <td className="itemsTitle" rowSpan="2">SQ. FEET<br/>PRICE</td>
                  <td className="itemsTitle" rowSpan="2">INST.<br/>FEE</td>
                  <td className="itemsTitle" colSpan="2">MAS OPEN</td>
                  <td className="itemsTitle">EST</td>
                  <td className="itemsTitle" rowSpan="2">TRK</td>
                  <td className="itemsTitle" rowSpan="2">TYPE</td>
                  <td className="itemsTitle" colSpan="2">UPPER</td>
                  <td className="itemsTitle" colSpan="2">LOWER</td>
                  <td className="itemsTitle" rowSpan="2">MOUNT</td>
                  <td className="itemsTitle" colSpan="2">{isPanels?"PANELS":"BLADES"}</td>
                  <td className="itemsTitle" colSpan="3" style={hideIfPanels}>ANGULAR</td>
                  <td className="itemsTitle" colSpan="3">EXTRA ANG</td>
                  <td className="itemsTitle" colSpan="3">TUBES</td>
                  <td className="itemsTitle" rowSpan="2" style={hideIfPanels}>LEFT</td>
                  <td className="itemsTitle" rowSpan="2" style={hideIfPanels}>RIGHT</td>
                  <td className="itemsTitle" colSpan="2" rowSpan="2" style={hideIfPanels}>LOCK</td>
                  <td className="itemsTitle" rowSpan="2">LN. FT</td>
                  <td className="itemsTitle" rowSpan="2">Total Sq.<br/>Feet</td>
                  <td className="itemsTitle" rowSpan="2" style={hideIfReadOnly}>&nbsp;</td>
                </tr>
                <tr>
                  <td className="itemsTitle">W</td>
                  <td className="itemsTitle">HT</td>
                  <td className="itemsTitle">+HT</td>
                  <td className="itemsTitle">SIZE</td>
                  <td className="itemsTitle">TYPE</td>
                  <td className="itemsTitle">SIZE</td>
                  <td className="itemsTitle">TYPE</td>
                  <td className="itemsTitle">LONG</td>
                  <td className="itemsTitle">QTY</td>
                  <td className="itemsTitle" style={hideIfPanels}>TYPE</td>
                  <td className="itemsTitle" style={hideIfPanels}>SIZE</td>
                  <td className="itemsTitle" style={hideIfPanels}>QTY</td>
                  <td className="itemsTitle">TYPE</td>
                  <td className="itemsTitle">LONG</td>
                  <td className="itemsTitle">QTY</td>
                  <td className="itemsTitle">TYPE</td>
                  <td className="itemsTitle">SIZE</td>
                  <td className="itemsTitle">QTY</td>
                </tr>
                {itemComponents}
              </tbody>
            </table>
            <input type="button" value="NEW" onClick={this.newPOItem.bind(this)} style={hideIfReadOnly}/>
          </div>
        </form>
      </div>
    )
  }

  newPOItem() {
    var newItem = this.props.po.newItem();
    setTimeout(() => {
      var productDropdownId = "#item-"+this.props.po.PONUMBER+"-"+newItem.LineNumber+" .PRODUCTTYPE_Field";
      var productDropdown = $(productDropdownId).get(0);
      productDropdown.focus();
    }, 0);
  }
}