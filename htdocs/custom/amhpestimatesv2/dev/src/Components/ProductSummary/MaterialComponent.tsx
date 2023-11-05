import ProductComponent, { ProductComponentProps } from './ProductComponent';
import { Material } from '../../store/Material';

export interface MaterialProps extends ProductComponentProps {
    item: Material
    readOnly: boolean
}

const MaterialComponent = ({ item, ...rest }: MaterialProps) => {
    return (
        <ProductComponent
            item={item} 
            description={
                <>
                    <p className='table_text'>Size: {item.widthtxt||"?"} x {item.heighttxt||"?"} x {item.lengthtxt||"?"}</p>
                    <p className='table_text'>Color: {item.color}</p>
                </>
            }

            {...rest}
        />)
};

export default MaterialComponent;

