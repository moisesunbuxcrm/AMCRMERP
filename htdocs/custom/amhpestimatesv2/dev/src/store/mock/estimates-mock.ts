import { EstimateStatus } from "../../types/EstimateStatus"
import ItemType from "../../types/ItemType"
import ModuleType from "../../types/ModuleType"
import { ModifiedState } from "../../types/Status"
import WinType from "../../types/WinType"
import { Estimate } from "../Estimate"
import { EstimateItem } from "../EstimateItem"
import { Hardware } from "../Hardware"
import { ImpactProduct } from '../ImpactProduct'
import { c1 } from "./customers-mock"

const ip_base1 = (id:number, eid:number, ino:number):EstimateItem => ({
  "_modified":ModifiedState.none,
  "color": "BRONZE",
  "cost_price": 200+id,
  "estimateid": eid,
  "finalprice": 500+id,
  "id": id,
  "image": "/AMCRMERP/htdocs/document.php?modulepart=product&attachment=0&file=36.75X50.375HRXOECOBB5.16C/36.75X50.375.png",
  "inst_discount": 0.1,
  "inst_price": 300+id,
  "itemno": ino,
  "itemtype": ItemType.Window,
  "modtype": ModuleType.ImpactProduct,
  "name": `Eco window 36.75x25.75HRXOECOBB5.16C ${id}`,
  "otherfees": 40 + id,
  "sales_discount": 0.1,
  "sales_price": 300 + id,
  "wintype": WinType.SingleHungSeries,
})

const hw_base1 = (id:number, eid:number, ino:number):EstimateItem => ({
  "_modified":ModifiedState.none,
  "color": "BRONZE",
  "cost_price": 200+id,
  "estimateid": eid,
  "finalprice": 300+id,
  "id": id,
  "image": "/AMCRMERP/htdocs/document.php?modulepart=product&entity=1&file=%2F619CENF58%26LATF59X%2F619CENF58%26LATF59.png",
  "inst_discount": 0.1,
  "inst_price": 100+id,
  "itemno": ino,
  "itemtype": ItemType.Hardware,
  "modtype": ModuleType.Hardware,
  "name": `SATIN NICKEL CENTURY F58 & LAT F59 ACTIVE ${id}`,
  "otherfees": 30 + id,
  "sales_discount": 0.1,
  "sales_price": 200 + id,
  "wintype": WinType.None,
})

const ip1 = (id:number, eid:number, ino:number):ImpactProduct => ({
  ...ip_base1(id, eid, ino),
  "coating": "NONE",
  "colonial_across": 10+id,
  "colonial_down": 20+id,
  "colonial_fee": 0+id,
  "configuration": "XOX",
  "frame_color": "BRONZE",
  "glass_color": "BRONZE",
  "glass_type": "Laminated",
  "height": 50.375,
  "heighttxt": "50 3/8",
  "interlayer": "WHITE",
  "is_colonial": true,
  "is_def_color": true,
  "is_def_glass_color": true,
  "is_screen": false,
  "is_standard": true,
  "length": 5,
  "lengthtxt": "5",
  "product_ref": `26.25X50.375SHELECOWG5.16C`,
  "provider": "ECO WINDOWS",
  "room_description":"Main Bedroom",
  "roomnum": 1,
  "roomtype": 1,
  "floornum": 1,
  "width": 26.25,
  "widthtxt": "26 1/4",
})

const hw1 = (id:number, eid:number, ino:number):Hardware => ({
  ...hw_base1(id, eid, ino),
  "configuration": "X",
  "hardwaretype": "French Door",
  "provider": "MILLWORK",
  "product_ref": `619CENF58&LATF59X`,
})

const e_base = (id:number):Estimate => {
  const c = c1(id+1000)
  return {
    "_modified":ModifiedState.none,
    "add_inst_discount":0.3,
    "add_sales_discount":0.16,
    "approved_date":"2021-05-07 02:50:02",
    "customerid":c.id,
    "defcolor":"BRONZE",
    "defglasscolor":"BRONZE",
    "delivered_date":undefined,
    "deposit_percent_with_install":40,
    "deposit_percent":50,
    "estimatenum":"E00002",
    "folio":"04-3117-016-0342",
    "id":id,
    "is_alteration":true,
    "is_installation_included":true,
    "new_construction_owner_responsability":false,
    "pay_upon_completion":false,
    "percent_final_inspection":10,
    "permitId":0,
    "permits":500,
    "quotedate": "2021-05-06 02:50:02",
    "rejected_date":undefined,
    "salestax":.26,
    "status":EstimateStatus.InProgress,
    "status_reason":"",
    "totalprice":1001.0000,
    "vendor":"Paul Dermody",
    "vendor_phone":"123 456 7890",
    "warranty_years":10,
    "qualifiername": "Jose G Perez",
    "customer": c,
    "items": []
  }
}

const ip_missingtype = (id:number, eid:number, ino:number):ImpactProduct => ({
  ...ip1(id, eid, ino),
  "roomtype": undefined,
})

const ip_missingroomnum = (id:number, eid:number, ino:number):ImpactProduct => ({
  ...ip1(id, eid, ino),
  "roomnum": undefined,
})

const ip_withescape = (id:number, eid:number, ino:number):ImpactProduct => ({
  ...ip1(id, eid, ino),
  "wintype": WinType.SingleHungSeries,
  "width": 100,
  "height": 56.75,
  "length": 5,
})

const ip_withnoescape = (id:number, eid:number, ino:number):ImpactProduct => ({
  ...ip1(id, eid, ino),
  "wintype": WinType.SingleHungSeries,
  "width": 100,
  "height": 56.749,
  "length": 5,
})

const ip_withclearbath = (id:number, eid:number, ino:number):ImpactProduct => ({
  ...ip1(id, eid, ino),
  "roomtype": 2,
  "interlayer": "CLEAR"
})


// An estimate that generates no warnings or errors 
const e_nowarnings = (id:number):Estimate => {
  let itemid=100+id;
  let itemno=1
  return {
    ...e_base(id),
    "items": [
      {
        ...ip_withescape(itemid++, id, itemno++),
        "roomnum": 1
      } as ImpactProduct,
      {
        ...ip_withnoescape(itemid++, id, itemno++),
        "roomnum": 1
      } as ImpactProduct,
      {
        ...hw1(itemid++, id, itemno++),
      } as Hardware,
    ]
  }
}

// An estimate that generates all warnings and errors 
const e_allwarnings = (id:number):Estimate => {
  let itemid=100+id;
  let itemno=1
  return {
    ...e_base(id),
    "items": [
      {
        ...ip_missingtype(itemid++, id, itemno++),
        "roomnum": 1
      } as ImpactProduct,
      {
        ...hw1(itemid++, id, itemno++),
      } as Hardware,
      {
        ...ip_missingroomnum(itemid++, id, itemno++),
      } as ImpactProduct,
      {
        ...ip_withescape(itemid++, id, itemno++),
        "roomnum": 3
      } as ImpactProduct,
      {
        ...ip_withnoescape(itemid++, id, itemno++),
        "roomnum": 3
      } as ImpactProduct,
      {
        ...ip_withnoescape(itemid++, id, itemno++),
        "roomnum": 4
      } as ImpactProduct,
      {
        ...ip_withnoescape(itemid++, id, itemno++),
        "roomnum": 4
      } as ImpactProduct,
      {
        ...ip_withclearbath(itemid++, id, itemno++),
        "roomnum": 5
      } as ImpactProduct,
      {
        ...ip_withclearbath(itemid++, id, itemno++),
        "roomnum": 6
      } as ImpactProduct,
    ]
  }
}

export const GetEstimate = (id:number):Estimate => {
  let e:Estimate = e_allwarnings(id)
  switch(id) {
    case 1: e = e_allwarnings(id)
      break;
    case 2: e = e_nowarnings(id)
      break;
  }

  return e;
}
