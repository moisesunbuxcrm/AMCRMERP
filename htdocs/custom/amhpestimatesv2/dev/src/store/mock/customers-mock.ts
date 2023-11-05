import { CustomerDetails } from "../customersSlice"

export const CustomerNames = [
    { "value": "4504", "label": "10300 CORAL WAY LLC" }, 
    { "value": "4646", "label": "10300 INVESTMENT LLC" }, 
    { "value": "5744", "label": "11020 PEACHTREE DRIVE LLC" }, 
    { "value": "7825", "label": "1125 ALHAMBRA LLC" }, 
    { "value": "6859", "label": "114 MENORES LLC" }, 
    { "value": "5733", "label": "1171 MANAGEMENT LLC" }, 
    { "value": "2826", "label": "12687 INVESTMENT CORP" }, 
    { "value": "3923", "label": "13260658 LLC TRS" }, 
    { "value": "4448", "label": "13306 BY THE SEA CORP" }, 
    { "value": "8882", "label": "1361 SW 124 CT'" }, 
    { "value": "7168", "label": "14170 SW 260 ST 102 MIAMI FLORIDA 33032 LLC" }, 
    { "value": "2020", "label": "14170 SW 260 ST 102 MIAMI FLORIDA 33032 LLC" }, 
    { "value": "839", "label": "147 BIRD ROAD DEVELOPMENT INC" }]

export const c1 = (id:number):CustomerDetails => ({
    "contactaddress":`contactaddress_${id}`,
    "contactmobile":`contactmobile_${id}`,
    "contactname":`contactname_${id}`,
    "contactphone":`contactphone_${id}`,
    "customeraddress":`${id} NE 158 ST`,
    "customercity":`North Miami Beach`,
    "customeremail":`email${id}@hotmail.com`,
    "customermobile":`customermobile_${id}`,
    "customername":`Name_${id}`,
    "customerphone":`(305)968-780${id}`,
    "customerstate":"Florida",
    "customerzip":`33161`,
    "folionumber":`123456789${id}`,
    "id":id,
    "reltype":2,
})
    
export const GetCustomerDetails = (id:number, name: string):CustomerDetails => {
    let c = c1(id)
    return {
        ...c,
        "customername":name??c.customername,
    }
}