import { EstimateStatus } from "../types/EstimateStatus";
import { isDeleted, isModified, ModifiedState } from "../types/Status";
import { CustomerDetails } from "./customersSlice";
import { EstimateItem } from "./EstimateItem";

export type Estimate = {
  _modified:ModifiedState
  add_inst_discount?: number
  add_sales_discount?: number
  customerid?: number
  defcolor?: string
  defglasscolor?: string
  estimatenum?: string
  folio?: string
  deposit_percent?: number
  deposit_percent_with_install?: number
  percent_final_inspection?: number
  warranty_years?: number
  pay_upon_completion?: boolean
  new_construction_owner_responsability?: boolean
  status?: EstimateStatus
  status_reason?: string
  approved_date?: string
  rejected_date?: string
  delivered_date?: string
  permitId?: number
  id?: number
  is_alteration?: boolean
  is_installation_included?: boolean
  notes?: string
  public_notes?: string
  permits?: number
  quotedate?: string
  salestax?: number
  totalprice?: number
  vendor?: string
  vendor_phone?: string
  qualifiername?: string

  customer?: CustomerDetails
  items: EstimateItem[]
}

export const createDefaultEstimate = (vendor:string, vendor_phone:string, customerid?:number):Estimate => ({
  _modified: ModifiedState.new,
  add_inst_discount: 0,
  add_sales_discount: 0,
  approved_date: undefined,
  customerid: customerid,
  defcolor: "WHITE",
  defglasscolor: "WHITE",
  delivered_date: undefined,
  deposit_percent_with_install: 0,
  deposit_percent: 0,
  estimatenum: undefined,
  folio: undefined,
  id: undefined,
  is_alteration: false,
  is_installation_included: true,
  new_construction_owner_responsability: false,
  notes: "",
  public_notes: "",
  pay_upon_completion: true,
  percent_final_inspection: 0,
  permitId: 0,
  permits: 700,
  quotedate: formatDate(new Date()),
  rejected_date: undefined,
  salestax: 0,
  status: EstimateStatus.InProgress,
  status_reason: "",
  totalprice: 0,
  vendor,
  vendor_phone,
  warranty_years: 5,
  qualifiername: undefined,

  items: []
})

export const formatDate  = (d:Date|undefined) => {
  const twoDigits = (n:number) => n>9 ? n.toString() : "0"+n
  return d ? d.getFullYear() + "-" + twoDigits(d.getMonth()+1) + "-" + twoDigits(d.getDate())  + " " + twoDigits(d.getHours()) + ":" + twoDigits(d.getMinutes()) + ":" + twoDigits(d.getSeconds()) : ""
}

export const isDirty = (e:Estimate):boolean => {
  let dirty = isModified(e._modified);
  if (!dirty && e.items) {
    for (let i = 0; !dirty && i < e.items.length; i++) {
      const item = e.items[i];
      dirty = isModified(item._modified)
    }
  }
  return dirty;
}
export const getItems = (e:Estimate) => e.items ? e.items.filter(i => !isDeleted(i._modified)) : []
