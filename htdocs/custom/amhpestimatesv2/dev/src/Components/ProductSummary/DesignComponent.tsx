import ProductComponent, { ProductComponentProps } from './ProductComponent';
import { Design } from '../../store/Design';

export interface DesignProps extends ProductComponentProps {
    item: Design
    readOnly: boolean
}

const DesignComponent = ({ item, ...rest }: DesignProps) => {
    return (
        <ProductComponent
            item={item} 
            description={
                <>
                    <p className='table_text'>Size: {item.widthtxt||"?"} x {item.heighttxt||"?"}</p>
                    <p className='table_text'>Color: {item.color}</p>
                </>
            }

            {...rest}
        />)
};

export default DesignComponent;

