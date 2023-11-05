import React from "react"
import ReactDOM from 'react-dom';
import { observer } from 'mobx-react'
import { action, extendObservable } from 'mobx';

@observer
export default class CustomerDropdown extends React.Component {
	constructor(data, initialStatus, isNew) {
		super();
		extendObservable(this, {
			_customerMap: null,
			_options: null,
			_dataPromise: null,
		});
	}

	componentDidMount()
	{
		if (!this.props.readOnly)
		{
			this.loadItems(); // Loads customers asynchronously
		}
	}

	render() {
		var po = this.props.po;

		if (this.props.readOnly)
			return <span>{po.CUSTOMERNAME}</span>;

		if (this._options == null)
			return <span>Loading...</span>;
		if (this._options.length == 0)
			return <span>No customers found...</span>;
		
		var custId = this.props.po.customerId;
		if (custId == null)
			custId=0;
		return <select value={custId} className="flat minwidth100" onChange={this.onChange.bind(this)}>
				{this._options}
			</select>;

	}

	@action loadItems()
	{
		if (this._dataPromise != null)
			return this._dataPromise;

		this._dataPromise = $.ajax({
			url: "db/getThirdPartiesForEstimate.php",
			dataType: 'json'
		}).then(action("CustomerDropdown.loadItems().success", data => {
			if (data)
			{
				this._customerMap = {};
				this._options = [<option key="0" value="0">Choose One...</option>];
				for (var i in data) {
					var c = data[i];
					this._customerMap[c.rowid] = c;
					this._options.push(<option key={c.rowid} value={c.rowid}>{c.CUSTOMERNAME}</option>);
				}
			}
		}),
		err => console.log(err))
		.then(data => {
			var sel = ReactDOM.findDOMNode(this);
			$(sel).select2({
				dir: 'ltr',
				width: 'resolve',
				minimumInputLength: 0,
				containerCssClass: ':all:', /* Line to add class of origin SELECT propagated to the new <span class="select2-selection...> tag */
				templateSelection: function (selection) { /* Format visible output of selected value */
					return selection.text;
				},
				escapeMarkup: function(markup) {
					return markup;
				},
				dropdownCssClass: 'ui-dialog'
			});
			$(sel).on("change", this.onChange.bind(this)); 
		});
	}

	@action onChange(e)
	{
		var custId = e.target.value;
		this.props.po.setProperty("customerId", custId);
		this.props.po.setProperty("CUSTOMERNAME", custId == 0 ? "" : this._customerMap[custId].CUSTOMERNAME);
		this.props.po.setProperty("CONTACTNAME", custId == 0 ? "" : this._customerMap[custId].CONTACTNAME);
		this.props.po.setProperty("CONTACTPHONE1", custId == 0 ? "" : this._customerMap[custId].CONTACTPHONE1);
		this.props.po.setProperty("CONTACTPHONE2", custId == 0 ? "" : this._customerMap[custId].CONTACTPHONE2);
		this.props.po.setProperty("CUSTOMERADDRESS", custId == 0 ? "" : this._customerMap[custId].CUSTOMERADDRESS);
		this.props.po.setProperty("ZIPCODE", custId == 0 ? "" : this._customerMap[custId].ZIPCODE);
		this.props.po.setProperty("CITY", custId == 0 ? "" : this._customerMap[custId].CITY);
		this.props.po.setProperty("STATE", custId == 0 ? "" : this._customerMap[custId].STATE);
		this.props.po.setProperty("PHONENUMBER1", custId == 0 ? "" : this._customerMap[custId].PHONENUMBER1);
		this.props.po.setProperty("PHONENUMBER2", custId == 0 ? "" : this._customerMap[custId].PHONENUMBER2);
		this.props.po.setProperty("FAXNUMBER", custId == 0 ? "" : this._customerMap[custId].FAXNUMBER);
		this.props.po.setProperty("EMail", custId == 0 ? "" : this._customerMap[custId].EMail);
	}
  }