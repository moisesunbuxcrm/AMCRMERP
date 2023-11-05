import Fraction from "fraction.js"
import { itemType2Id } from "../../types/ItemType"
import ModuleType from "../../types/ModuleType"
import WinType from "../../types/WinType"
import { ProductDetails, ProductSummary } from "../productsSlice"

export const ProductSummaries:ProductSummary[] = [
  { 
    "color": "BRONZE", 
    "heighttxt": "25 3/4",
    "lengthtxt": "2 1/4",
    "id": 522, 
    "itemtype": 1, 
    "modtype": ModuleType.ImpactProduct,
    "glass_color": "BRONZE",
    "interlayer": "CLEAR",
    "wintype": WinType.SingleHungSeries,
    "name": "MyImpactProduct1", 
    "provider": "ECO WINDOWS", 
    "ref": "MyImpactProduct1", 
    "widthtxt": "105 3/4", 
  }, 
  { 
    "color": "WHITE", 
    "heighttxt": "50 3/8",
    "lengthtxt": "2 1/4",
    "id": 32, 
    "itemtype": 1, 
    "modtype": ModuleType.ImpactProduct,
    "glass_color": "BRONZE",
    "interlayer": "CLEAR",
    "wintype": WinType.SingleHungSeries,
    "name": "MyImpactProduct2", 
    "provider": "ECO WINDOWS", 
    "ref": "MyImpactProduct2", 
    "widthtxt": "26 1/4", 
  }, 
  { 
    "color": "WHITE",
    "heighttxt": "62 3/4",
    "lengthtxt": "2 1/4",
    "id": 256, 
    "itemtype": 1, 
    "modtype": ModuleType.ImpactProduct,
    "glass_color": "BRONZE",
    "interlayer": "CLEAR",
    "wintype": WinType.FrenchDoor,
    "name": "MyImpactProduct3", 
    "provider": "PGT WINDOWS", 
    "ref": "MyImpactProduct3", 
    "widthtxt": "19 1/8", 
  }, 
  { 
    "color": "GRAY", 
    "heighttxt": "",
    "lengthtxt": "2 1/4",
    "id": 1768, 
    "itemtype": 3, 
    "modtype": ModuleType.Hardware,
    "glass_color": "WHITE",
    "interlayer": "WHITE",
    "wintype": WinType.SingleHungSeries,
    "name": "MyImpactProduct4", 
    "provider": "MILLWORK", 
    "ref": "MyImpactProduct4", 
    "widthtxt": "", 
  },
  { 
    "color": "WHITE", 
    "heighttxt": "4 1/2",
    "lengthtxt": "2 1/4",
    "id": 2083, 
    "itemtype": 3, 
    "modtype": ModuleType.Material,
    "glass_color": "",
    "interlayer": "",
    "wintype": WinType.SingleHungSeries,
    "name": "MyMaterial1", 
    "provider": "", 
    "ref": "MyMaterial1", 
    "widthtxt": "3", 
  },
  { 
    "color": "WHITE", 
    "heighttxt": "4 1/2",
    "lengthtxt": "",
    "id": 2093, 
    "itemtype": 3, 
    "modtype": ModuleType.Design,
    "glass_color": "",
    "interlayer": "",
    "wintype": WinType.SingleHungSeries,
    "name": "MyDesign1", 
    "provider": "", 
    "ref": "MyDesign1", 
    "widthtxt": "3", 
  }
]

export const GetProductDetails = (id: number): ProductDetails => {
  let ps = ProductSummaries.find(ps => ps.id === id)
  ps = ps ?? {
    "color": "BRONZE",
    "heighttxt": "25 3/4",
    "lengthtxt": "2 1/4",
    "id": id,
    "name": "ECO 36 3/4 X 25 3/4 BRONZE/BRONZE CLEAR INTERLAYER",
    "itemtype": 1, 
    "modtype": ModuleType.ImpactProduct,
    "glass_color": "BRONZE",
    "interlayer": "WHITE",
    "wintype": WinType.SingleHungSeries,
    "provider": "ECO WINDOWS",
    "ref": "36.75X25.75HRXOECOBB5.16C",
    "widthtxt": "36 3/4",
  }

  const wf = new Fraction(ps.widthtxt ? ps.widthtxt : 0)
  const hf = new Fraction(ps.heighttxt ? ps.heighttxt : 0)
  const lf = new Fraction(ps.lengthtxt ? ps.lengthtxt : 0)
  const w = ps.widthtxt ? wf.valueOf() : 0
  const h = ps.heighttxt ? hf.valueOf() : 0
  const l = ps.lengthtxt ? lf.valueOf() : 0

  let p:ProductDetails = {
    ...ps,
    coating: "",
    configuration: "",
    cost_price: 80,
    frame_color: "",
    glass_type: "",
    hardwaretype: "",
    height: h,
    image: "/AMCRMERP/htdocs/document.php?modulepart=product&attachment=0&file=" + (ps.id === 256 ? "36.75X25.75HRXOECOBB5.16C/36.75X25.75.png" : "19.125X25.75SHELECOBB5.16W/26.png"),
    inst_price: 0,
    is_screen: false,
    itemtype: itemType2Id(""),
    length: l,
    sales_price: 100,
    width: w,
  }
  
  switch(p.modtype) {
    case ModuleType.Material:
    case ModuleType.Design:
      p = {
        ...p,
        itemtype: itemType2Id("Hardware"),
      }
      break
    default:
      p = {
        ...p,
        coating: "NONE",
        configuration: "X",
        frame_color: "BRONZE",
        glass_type: "Insulated",
        inst_price: 150,
        is_screen: true,
        itemtype: itemType2Id("Window"),
      }
  }

  return p
}
