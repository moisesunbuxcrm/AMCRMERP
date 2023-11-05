import { Estimate } from '../../store/Estimate';
import { Hardware } from '../../store/Hardware';
import ProductComponent, { ProductComponentProps } from './ProductComponent';

export interface HardwareComponentProps extends ProductComponentProps {
    e: Estimate
    item: Hardware
    readOnly: boolean
}

const HardwareComponent = ({ e, item, ...rest }: HardwareComponentProps) => {
    return (
        <ProductComponent
            e={e}
            item={item} 
            description={
                <>
                    <p className='table_text'>Provider: {item.provider}</p>
                    <p className='table_text'>Reference: {item.product_ref}</p>
                    <p className='table_text'>Hardware Type: {item.hardwaretype}</p>
                    <p className='table_text'>Configuration: {item.configuration}</p>        
                </>
            }
            {...rest}
        />)
};

export default HardwareComponent;