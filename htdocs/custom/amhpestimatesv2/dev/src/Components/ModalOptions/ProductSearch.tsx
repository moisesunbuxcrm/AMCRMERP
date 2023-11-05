import { useCallback, useEffect, useState } from 'react';
import { Modal, Select, Form, Button } from 'antd';
import './styles.css';
import { useAppSelector } from '../../store/hooks';
import * as ds from '../../store/defaultsSlice';
import * as ps from '../../store/productsSlice';
import { LoadingState } from '../../types/Status';
import { id2ItemType, itemType2Id, ItemTypes } from '../../types/ItemType';
import { WinTypes } from '../../types/WinType';

type ProductSearchProps = {
    visible: boolean
    currentID: number|undefined
    color: string
    glasscolor: string

    handleProduct: (id:number) => void
    handleCancel: () => void
}
const { Option } = Select;

const ProductSearch = (props: ProductSearchProps) => {
    const [id, setID] = useState(props.currentID)
    const [color, setColor] = useState(props.color)
    const [provider, setProvider] = useState("")
    const [itemtype, setItemType] = useState("")
    const [interlayer, setInterlayer] = useState("")
    const [wintype, setWintype] = useState("")
    const [modtype, setModtype] = useState("")
    const [glassColor, setGlassColor] = useState(props.glasscolor)
    const [ref, setRef] = useState("")
    
    const defaultStatus = useAppSelector(ds.selectDefaultsStatus)
    const defaults: ds.DefaultData | undefined = useAppSelector(ds.selectDefaults)
    const products: ps.ProductSummary[] | undefined = useAppSelector(ps.selectProductSummaries)
    const productsById: { [key: number]: ps.ProductSummary } | undefined = useAppSelector(ps.selectProductSummariesById)
    const productsStatus = useAppSelector(ps.selectProductSummariesStatus)

    const unspecified = useCallback((o:any) => o === undefined || o === "None" || o === "Any" || o === "" || o === "0" || o === 0, [])
    const ifUnspecified = useCallback((o:any, alt:any) => unspecified(o) ? alt : o, [unspecified])
    const id2ProductSummary = (id:number|undefined):ps.ProductSummary|undefined => productsById && id ? productsById[id] : undefined
    const prodSummary = id2ProductSummary(props.currentID)

    const resetSearch = useCallback(() => {
        setColor(prodSummary?.color || props.color || "Any")
        setProvider(ifUnspecified(prodSummary?.provider, "ECO WINDOWS"))
        setItemType(ifUnspecified(prodSummary?.itemtype&&id2ItemType(prodSummary?.itemtype).toString(), "Any"))
        setInterlayer(ifUnspecified(prodSummary?.interlayer, "CLEAR"))
        setWintype(ifUnspecified(prodSummary?.wintype, "Any"))
        setModtype(ifUnspecified(prodSummary?.modtype, "Any"))
        setGlassColor(prodSummary?.glass_color || props.glasscolor || "GRAY")
        setRef(ifUnspecified(prodSummary?.ref, "None"))
    }, [ifUnspecified, props.color, props.glasscolor, prodSummary?.color, prodSummary?.provider, prodSummary?.itemtype, prodSummary?.interlayer, prodSummary?.wintype, prodSummary?.modtype, prodSummary?.glass_color, prodSummary?.ref])

    const clearSearch = useCallback(() => {
        setColor(ifUnspecified(props.color, "Any"))
        setProvider("Any")
        setItemType("Any")
        setInterlayer("Any")
        setWintype("Any")
        setModtype("Any")
        setGlassColor(ifUnspecified(props.glasscolor, "Any"))
        setRef("None")
    }, [ifUnspecified, props.color, props.glasscolor])

    useEffect(() => {
        resetSearch()
    }, [props.visible, props.color, props.glasscolor, resetSearch])

    const filterOption = (input: any, option: any) => {
        const v = option.value.toLowerCase()
        const i = input.toLowerCase()
        return i==="" || (v !== null && (v?.indexOf(i) >= 0 || v.indexOf(i) >= 0))
    }

    const product2Label = (p:ps.ProductSummary|undefined):string => {
        if (p)
            return p.ref
        return "Any"
    }
    const id2Ref = (id:number) => id2ProductSummary(id)?.ref
    const isVisibleProductSummary = (p:ps.ProductSummary) => {
        let visible:boolean = true
        visible = visible && (unspecified(color) || p.color === color)
        visible = visible && (unspecified(provider) || p.provider === provider)
        visible = visible && (unspecified(itemtype) || p.itemtype === itemType2Id(itemtype))
        visible = visible && (unspecified(interlayer) || p.interlayer === interlayer)
        visible = visible && (unspecified(wintype) || p.wintype === wintype)
        visible = visible && (unspecified(modtype) || p.modtype === modtype)
        visible = visible && (unspecified(glassColor) || p.glass_color === glassColor)
        return visible
    } 

    const filterReferencesOptionsOnKeyPress = (input: any, option: any) => {
        let p = unspecified(option.key)?undefined:id2ProductSummary(option.key)
        let match:boolean = p!==undefined && isVisibleProductSummary(p)
        match = match && filterOption(input,option)
        return match
    }
    const selectProduct = (id:number) => {
        setID(id)
        setRef(id2Ref(id)||"")
    }

    const interlayers:string[] = (() => {
        const ilmap:{[key:string]: boolean} = {}
        const il:string[] = []
        products?.forEach(p => {
            if (p.interlayer) {
                if (!ilmap[p.interlayer]) {
                    ilmap[p.interlayer] = true
                    il.push(p.interlayer)
                }
            }
        })
        return il
    })()

    const refs = products?.filter(isVisibleProductSummary).map(p => <Option value={product2Label(p)} key={p.id}>{product2Label(p)}</Option>)

    return (
        <Modal
            title="Search Products"
            visible={props.visible} 
            onCancel={props.handleCancel}
            onOk={() => id && props.handleProduct(id)}
            okButtonProps={{ disabled: !id }}
            footer={[
                <Button key="OK" onClick={() => id && props.handleProduct(id)} disabled={unspecified(ref)}>
                  OK
                </Button>,
                <Button key="Cancel"onClick={props.handleCancel}>
                  Cancel
                </Button>,
                <Button key="Clear"onClick={clearSearch} style={{ marginLeft: "1rem"}}>
                    Clear
                </Button>
            ]}
        >
            <Form
                name="product-search"
                labelCol={{ span: 8 }}
                wrapperCol={{ span: 16 }}
                autoComplete="off"
            >
                <Form.Item label="Provider">
                    <Select<string>
                        showSearch
                        className='input show1'
                        optionFilterProp="label"
                        onChange={(txt:string,option:any) => setProvider(option.value)}
                        filterOption={filterOption}
                        value={provider}
                        loading ={defaultStatus===LoadingState.loading}
                        placeholder="Select Provider..."
                    >
                        <Option value="Any" key="0">Any</Option>
                        {defaults?.providers.map(p => <Option value={p.label} key={p.value}>{p.label}</Option>)}
                    </Select>
                </Form.Item>

                <Form.Item label="Color">
                    <Select
                            showSearch
                            className='input show1'
                            optionFilterProp="value"
                            onChange={(input:string,option:any) => option&&setColor(option.key) }
                            filterOption={(input:string,option:any) => option?.value ? option.value.toString().toLowerCase().indexOf(input.toLowerCase()) >= 0 : true }
                            value={color}
                        >
                        <Option value="Any" key="Any">Any</Option>
                        {defaults?.colors.filter(cd => cd.label!=="NONE").map(cd => <Option value={cd.label} key={cd.label}>{cd.label}</Option>)}
                    </Select>
                </Form.Item>

                <Form.Item label="Glass Color">
                    <Select
                            showSearch
                            className='input show1'
                            optionFilterProp="value"
                            onChange={(input:string,option:any) => option&&setGlassColor(option.key) }
                            filterOption={(input:string,option:any) => option?.value ? option.value.toString().toLowerCase().indexOf(input.toLowerCase()) >= 0 : true }
                            value={glassColor}
                        >
                        <Option value="Any" key="Any">Any</Option>
                        {defaults?.colors.filter(cd => cd.label!=="NONE").map(cd => <Option value={cd.label} key={cd.label}>{cd.label}</Option>)}
                    </Select>
                </Form.Item>


                <Form.Item label="Type of Opening">
                    <Select 
                        className='input show1'
                        onChange={(input:string,option:any) => option&&setItemType(option.key) }
                        value={itemtype}
                    >
                        {ItemTypes.map(i => i==="None" ? "Any" : i).map(i => <Option value={i} key={i}>{i}</Option>)}
                    </Select>
                </Form.Item>

                <Form.Item label="Interlayer">
                    <Select 
                        className='input show1'
                        onChange={(input:string,option:any) => option&&setInterlayer(option.key) }
                        value={interlayer}
                    >
                        <Option value="Any" key="Any">Any</Option>
                        {interlayers.map(i => <Option value={i} key={i}>{i}</Option>)}
                    </Select>
                </Form.Item>

                <Form.Item label="Type">
                    <Select 
                        className='input show1'
                        onChange={(input:string,option:any) => option&&setWintype(option.key) }
                        value={wintype}
                    >
                        <Option value="Any" key="Any">Any</Option>
                        {WinTypes.map(i => i==="None" ? "Any" : i).map(i => <Option value={i} key={i}>{i}</Option>)}
                    </Select>
                </Form.Item>

                <Form.Item label={"References ("+refs?.length+")"}>
                    <Select
                        showSearch
                        className='input show1'
                        optionFilterProp="ref"
                        onChange={(input:string,option:any) => option ? selectProduct(Number(option.key)) : selectProduct(0) }
                        filterOption={filterReferencesOptionsOnKeyPress}
                        value={ref}
                        loading ={productsStatus===LoadingState.loading}
                        placeholder="Select Product Reference..."
                    >
                        <Option value="None" key="0">None</Option>
                        {refs}
                    </Select> 
                </Form.Item>
            </Form>
        </Modal>
    );
};

export default ProductSearch;