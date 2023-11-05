import { useCallback, useEffect, useMemo, useState } from 'react';
import { Modal, Select, Form, Button } from 'antd';
import './styles.css';
import { useAppSelector } from '../../store/hooks';
import * as ds from '../../store/defaultsSlice';
import * as ps from '../../store/productsSlice';
import { LoadingState } from '../../types/Status';
import ModuleType from '../../types/ModuleType';

type MaterialSearchProps = {
    visible: boolean
    currentID: number|undefined
    color: string

    handleProduct: (id:number) => void
    handleCancel: () => void
}
const { Option } = Select;

const MaterialSearch = (props: MaterialSearchProps) => {
    const [id, setID] = useState(props.currentID)
    const [color, setColor] = useState(props.color)
    const [provider, setProvider] = useState("")
    const [width, setWidth] = useState("")
    const [height, setHeight] = useState("")
    const [length, setLength] = useState("")
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
        setProvider(ifUnspecified(prodSummary?.provider, "Any"))
        setWidth(ifUnspecified(prodSummary?.widthtxt, "Any"))
        setHeight(ifUnspecified(prodSummary?.heighttxt, "Any"))
        setLength(ifUnspecified(prodSummary?.lengthtxt, "Any"))
        setRef(ifUnspecified(prodSummary?.ref, "None"))
    }, [prodSummary?.color, prodSummary?.provider, prodSummary?.widthtxt, prodSummary?.heighttxt, prodSummary?.lengthtxt, prodSummary?.ref, props.color, ifUnspecified])

    const clearSearch = useCallback(() => {
        setColor(ifUnspecified(props.color, "Any"))
        setProvider("Any")
        setWidth("Any")
        setHeight("Any")
        setLength("Any")
        setRef("None")
    }, [ifUnspecified, props.color])

    useEffect(() => {
        resetSearch()
    }, [props.visible, props.color, resetSearch])

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
    const materialProducts = useMemo(() => products?.filter(p => p.modtype === ModuleType.Material),  [products])
    const isCandidateProduct = useCallback((p:ps.ProductSummary) => {
        let visible:boolean = true
        visible = visible && (unspecified(color) || p.color === color)
        visible = visible && (unspecified(provider) || p.provider === provider)
        visible = visible && (unspecified(width) || p.widthtxt === width)
        visible = visible && (unspecified(height) || p.heighttxt === height)
        visible = visible && (unspecified(length) || p.lengthtxt === length)
        return visible
    }, [color, height, length, provider, unspecified, width])

    const filterReferencesOptionsOnKeyPress = (input: any, option: any) => {
        let p = unspecified(option.key)?undefined:id2ProductSummary(option.key)
        let match:boolean = p!==undefined && isCandidateProduct(p)
        match = match && filterOption(input,option)
        return match
    }
    const selectProduct = (id:number) => {
        setID(id)
        setRef(id2Ref(id)||"")
    }

    const candidateProducts = useMemo(() => materialProducts?.filter(isCandidateProduct),  [isCandidateProduct, materialProducts])
    const refs = useMemo(() => candidateProducts?.map(p => <Option value={product2Label(p)} key={p.id}>{product2Label(p)}</Option>), [candidateProducts])
    const widthValues = useMemo(() => {
        const wMap:{[key:string]: string} = {}
        materialProducts?.forEach(p => {
            if (p.widthtxt)
                wMap[p.widthtxt] = p.widthtxt
        })
        return Object.keys(wMap).map(w => <Option value={w} key={w}>{w}</Option>)
    }, [materialProducts])
    const heightValues = useMemo(() => {
        const hMap:{[key:string]: string} = {}
        materialProducts?.forEach(p => {
            if (p.heighttxt)
                hMap[p.heighttxt] = p.heighttxt
        })
        return Object.keys(hMap).map(h => <Option value={h} key={h}>{h}</Option>)
    }, [materialProducts])
    const lengthValues = useMemo(() => {
        const wMap:{[key:string]: string} = {}
        materialProducts?.forEach(p => {
            if (p.lengthtxt)
                wMap[p.lengthtxt] = p.lengthtxt
        })
        return Object.keys(wMap).map(l => <Option value={l} key={l}>{l}</Option>)
    }, [materialProducts])

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

                <Form.Item label="Width">
                    <Select
                            showSearch
                            className='input show1'
                            optionFilterProp="value"
                            onChange={(input:string,option:any) => option&&setWidth(option.key) }
                            filterOption={(input:string,option:any) => option?.value ? option.value.toString().toLowerCase().indexOf(input.toLowerCase()) >= 0 : true }
                            value={width}
                        >
                        <Option value="Any" key="Any">Any</Option>
                        {widthValues}
                    </Select>
                </Form.Item>

                <Form.Item label="Height">
                    <Select
                            showSearch
                            className='input show1'
                            optionFilterProp="value"
                            onChange={(input:string,option:any) => option&&setHeight(option.key) }
                            filterOption={(input:string,option:any) => option?.value ? option.value.toString().toLowerCase().indexOf(input.toLowerCase()) >= 0 : true }
                            value={height}
                        >
                        <Option value="Any" key="Any">Any</Option>
                        {heightValues}
                    </Select>
                </Form.Item>

                <Form.Item label="Length">
                    <Select
                            showSearch
                            className='input show1'
                            optionFilterProp="value"
                            onChange={(input:string,option:any) => option&&setLength(option.key) }
                            filterOption={(input:string,option:any) => option?.value ? option.value.toString().toLowerCase().indexOf(input.toLowerCase()) >= 0 : true }
                            value={length}
                        >
                        <Option value="Any" key="Any">Any</Option>
                        {lengthValues}
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

export default MaterialSearch;