import { CopyFilled, DeleteFilled, EditFilled } from '@ant-design/icons';
import { Row, Col, Image, Button } from 'antd';
import Text from "antd/lib/typography/Text";
import { Estimate } from '../../store/Estimate';
import { EstimateItem } from '../../store/EstimateItem';
import { ImpactProduct } from '../../store/ImpactProduct';
import { asMoney, asPercentage } from '../../types/formats';
import ModuleType from '../../types/ModuleType';
import './styles.css';

export interface ButtonProps {
    item: EstimateItem
    itemIndex?: number
    onAdd?: (e:any,index?:number) => void
    onEdit?: (e:any,item:EstimateItem) => void
    onDelete?: (e:any,item:EstimateItem) => void
    onCopy?: (e:any,item:EstimateItem) => void
    readOnly: boolean
}

export const ProductButtons = ({ item, itemIndex, onAdd, onEdit, onDelete, onCopy, readOnly }: ButtonProps) =>
    <>
        <Button disabled={readOnly} type="primary" shape="round" onClick={(e:any) => onCopy && onCopy(e, item)} icon={<CopyFilled />} style={{marginRight: ".5rem"}} title="Copy"/>
        <Button disabled={readOnly} type="primary" shape="round" onClick={(e:any) => onDelete && onDelete(e, item)} icon={<DeleteFilled />} style={{marginRight: ".5rem"}} title="Delete"/>
        <Button type="primary" shape="round" onClick={(e:any) => onEdit && onEdit(e, item)} icon={<EditFilled />}  title="Edit"/>
    </>

export interface ProductComponentProps {
    e: Estimate
    item: EstimateItem
    description?: JSX.Element
    onUpdateItemNo?: (val:number) => void
    onUpdateQuantity?: (val:number) => void
    readOnly: boolean
}

const ProductComponent = ({ e, item, description, onUpdateItemNo, onUpdateQuantity, readOnly }: ProductComponentProps) => {
    const qtyWidth = item.modtype === ModuleType.Material ? 2 : 1
    const updateItemNo = (val:string) => {
        const num:number = Number(val)
        if (onUpdateItemNo && !isNaN(num)) 
            onUpdateItemNo(num)
    }
    const updateQuantity = (val:string) => {
        const num:number = Number(val)
        if (onUpdateQuantity && !isNaN(num)) 
            onUpdateQuantity(num)
    }

    return (
        <>
            <Row gutter={[6, 6]} className="product-component" wrap={false}>
                <Col xs={1} sm={1} md={1} lg={1} xl={1}>
                    <div className='title_container'>
                        <p className='table_text'>#</p>
                    </div>
                    <div className='context_container'>
                        <Text editable={!readOnly && { onChange: updateItemNo }}>{item.itemno}</Text>
                    </div>
                </Col>
                <Col xs={6} sm={6} md={6} lg={6} xl={6}>
                    <div className='title_container'>
                        <p className='table_text'>Description</p>
                    </div>
                    <div className='context_container'>
                        <p className='table_text description'>{item.name}</p>
                        {description}
                    </div>
                </Col>
                <Col xs={5} sm={5} md={5} lg={5} xl={5}>
                    <div className='title_container'>
                        <p className='table_text'>Image</p>
                    </div>
                    <div className='context_container'>
                        <Image
                            src={(process.env.REACT_APP_URL_PREFIX||"") + item.image}
                            fallback="data:image/png;base64,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"
                        />
                    </div>
                </Col>
                <Col xs={qtyWidth} sm={qtyWidth} md={qtyWidth} lg={qtyWidth} xl={qtyWidth}>
                    <div className='title_container'>
                        <p className='table_text'>Qty</p>
                    </div>
                    <div className='context_container'>
                        {item.modtype === ModuleType.Material 
                            ? <Text editable={!readOnly && { onChange: updateQuantity }}>{item.quantity}</Text>
                            : <p className='table_text'>{item.quantity}</p> 
                        }
                    </div>
                </Col>
                <Col xs={3} sm={3} md={3} lg={3} xl={3}>
                    <div className='title_container'>
                        <p className='table_text'>Unit Price</p>
                    </div>
                    <div className='context_container'>
                        <p className='table_text'>{asMoney(item.sales_price)}</p>
                    </div>
                </Col>
                {item.modtype===ModuleType.ImpactProduct ? 
                    <Col xs={2} sm={2} md={2} lg={2} xl={2}>
                        <div className='title_container'>
                            <p className='table_text'>Colonial</p>
                        </div>
                        <div className='context_container'>
                            <p className='table_text'>{asMoney((item as ImpactProduct).colonial_fee)}</p>
                        </div>
                    </Col> : ""}
                {![ModuleType.Material, ModuleType.Design].includes(item.modtype) ? 
                    <Col xs={2} sm={2} md={2} lg={2} xl={2}>
                        <div className='title_container'>
                            <p className='table_text'>Install</p>
                        </div>
                        <div className='context_container'>
                            <p className='table_text'>{asMoney(e.is_installation_included ? item.inst_price : 0)}</p>
                        </div>
                    </Col> : ""}
                <Col xs={2} sm={2} md={2} lg={2} xl={2}>
                    {item.sales_discount && item.sales_discount > 0 ? 
                        <>
                            <div className='title_container'>
                                <p className='table_text'>Dsc</p>
                            </div>
                            <div className='context_container'>
                                <p className='table_text'>{asPercentage(item.sales_discount)}</p>
                            </div>
                        </> : 
                        <>
                            <div className='title_container'>
                                <p className='table_text'>&nbsp;</p>
                            </div>
                            <div className='context_container'>
                                <p className='table_text'>&nbsp;</p>
                            </div>
                        </>
                    }
                </Col>
                <Col xs={2} sm={2} md={2} lg={2} xl={2}>
                    <div className='title_container'>
                        <p className='table_text'>Total</p>
                    </div>
                    <div className='context_container'>
                        <p className='table_text'>{asMoney(item.finalprice)}</p>
                    </div>
                </Col>

            </Row>
        </>
    );
};

export default ProductComponent; 