import React from "react"
import { observer } from 'mobx-react'
import POText from '../Fields/POText'
import PONumber from '../Fields/PONumber'
import ErrorBoundary from "../Utils/ErrorBoundary";
import PODropdown from '../Fields/PODropdown'

@observer
export default class ProductionOrderItem extends React.Component { 
	state = {
		overrideInstFeeReadOnlyFlag: false
	};

	onMouseClickInstFee = e => {
		var item = this.props.item;
		var readOnly = this.props.readOnly;
		if (!readOnly && e.altKey && e.ctrlKey) {
			this.setState({
				overrideInstFeeReadOnlyFlag: true
			});
		}
	}

	render() {
		var item = this.props.item;
		var readOnly = this.props.readOnly;
		var isPanels = item.po.TYPE == "PANELS";
		var hideIfPanels = isPanels ? { display: "none" } : null;
	    var hideIfReadOnly = readOnly ? { display: "none" } : null;
		var instFeeReadOnly = readOnly || !['2','5'].includes(item.PRODUCTTYPE);
		if (this.state.overrideInstFeeReadOnlyFlag)
			instFeeReadOnly= false;

		return (
			<ErrorBoundary>
				<tr id={"item-"+item.po.PONUMBER+"-"+item.LineNumber}>
					<td className="itemValue"><PONumber		readOnly={readOnly}	item={item} size="1" prop="LineNumber"/></td>
					<td className="itemValue"><PODropdown	readOnly={readOnly} valueIsName="false" item={item} items={estimateData.producttypes} prop="PRODUCTTYPE"/></td>
					<td className="itemValue"><PODropdown	readOnly={readOnly} item={item} items={estimateData.materials} prop="MATERIAL" allowEmpty="true" emptyVal="None" emptyName=""/></td>
					<td className="itemValue"><PODropdown	readOnly={readOnly} item={item} items={estimateData.windowtypes} prop="WINDOWSTYPE"/></td>
					<td className="itemValue"><PODropdown	readOnly={readOnly} item={item} items={estimateData.colors} prop="COLOR"/></td>
					<td className="itemValue"><PODropdown	readOnly={readOnly} valueIsName="false" item={item} items={estimateData.stackingcharts} prop="PROVIDER"/></td>
					<td className="itemValue"><PONumber 	readOnly={readOnly} size="4" item={item} prop="SQFEETPRICE"/></td>
					<td className="itemValue"><PONumber		readOnly={instFeeReadOnly} item={item} prop="INSTFEE" onDoubleClick={this.onMouseClickInstFee}/></td>
					<td className="itemValue"><PONumber 	readOnly={readOnly} item={item} size="4" prop="OPENINGW"/></td>
					<td className="itemValue"><PONumber 	readOnly={readOnly} item={item} size="4" prop="OPENINGHT"/></td>
					<td className="itemValue"><PONumber 	readOnly="true"	item={item} prop="EST8HT"/></td>
					<td className="itemValue"><PONumber 	readOnly={readOnly} item={item} size="4" prop="TRACK"/></td>
					<td className="itemValue"><PODropdown	readOnly={readOnly} item={item} items={estimateData.itemtypes} prop="TYPE"/></td>
					<td className="itemValue"><PONumber 	readOnly="true" item={item} prop="UPPERSIZE"/></td>
					<td className="itemValue"><PODropdown	readOnly={readOnly} item={item} items={estimateData.itemtypes} prop="UPPERTYPE"/></td>
					<td className="itemValue"><PONumber 	readOnly="true" item={item} prop="LOWERSIZE"/></td>
					<td className="itemValue"><PODropdown	readOnly={readOnly} item={item} items={estimateData.itemtypes} prop="LOWERTYPE"/></td>
					<td className="itemValue"><PODropdown	readOnly={readOnly} item={item} items={estimateData.mounts} prop="MOUNT"/></td>
					<td className="itemValue"><PONumber 	readOnly="true"  item={item} prop="BLADESLONG"/></td>
					<td className="itemValue"><PONumber 	readOnly="true" item={item} prop="BLADESQTY"/></td>
					<td className="itemValue" style={hideIfPanels}><PODropdown	readOnly={readOnly} item={item} items={estimateData.angulartypes} prop="ANGULARTYPE"/></td>
					<td className="itemValue" style={hideIfPanels}><PONumber size="4" readOnly={readOnly} item={item} prop="ANGULARSIZE"/></td>
					<td className="itemValue" style={hideIfPanels}><PONumber size="4" readOnly={readOnly} item={item} prop="ANGULARQTY"/></td>
					<td className="itemValue"><PODropdown	readOnly={readOnly} item={item} items={estimateData.angulartypes} prop="EXTRAANGULARTYPE" allowEmpty="true" emptyVal="0" emptyName="N/A"/></td>
					<td className="itemValue"><PONumber 	readOnly={readOnly} size="4" item={item} prop="EXTRAANGULARSIZE"/></td>
					<td className="itemValue"><PONumber 	readOnly={readOnly} size="4" item={item} prop="EXTRAANGULARQTY"/></td>
					<td className="itemValue"><PODropdown	readOnly={readOnly} item={item} items={estimateData.angulartypes} prop="TUBETYPE" allowEmpty="true" emptyVal="0" emptyName="N/A"/></td>
					<td className="itemValue"><PONumber 	readOnly={readOnly} size="4" item={item} prop="TUBESIZE"/></td>
					<td className="itemValue"><PONumber 	readOnly={readOnly} size="4" item={item} prop="TUBEQTY"/></td>
					<td className="itemValue" style={hideIfPanels}><PONumber readOnly={readOnly} item={item} prop="LEFT"/></td>
					<td className="itemValue" style={hideIfPanels}><PONumber readOnly={readOnly} item={item} prop="RIGHT"/></td>
					<td className="itemValue" style={hideIfPanels}><PODropdown	size="4" readOnly={readOnly} item={item} items={estimateData.lockins} prop="LOCKIN" allowEmpty="true" emptyVal="" emptyName=""/></td>
					<td className="itemValue" style={hideIfPanels}><PODropdown size="4" readOnly={readOnly} item={item} items={estimateData.locksizes} prop="LOCKSIZE" allowEmpty="true" emptyVal="" emptyName=""/></td>
					<td className="itemValue"><PONumber 	readOnly="true"	item={item} prop="LINEARFT"/></td>
					<td className="itemValue"><PONumber 	readOnly="true"	item={item} prop="ALUM"/></td>
					<td style={hideIfReadOnly}>
						<span style={{whiteSpace: "nowrap"}}>
							<img src="../../theme/eldy/img/delete.png"  onClick={this.delItem.bind(this)} />
							<img src="../../theme/eldy/img/filenew.png" onClick={this.duplicateItem.bind(this)} />
						</span>
					</td>
				</tr>
			</ErrorBoundary>
		)
	}
	
	delItem(e) {
		e.preventDefault();
		console.log(e);
		this.props.item.delete();
	}

	duplicateItem(e) {
		e.preventDefault();
		this.props.item.po.duplicateItem(this.props.item);
	}

}