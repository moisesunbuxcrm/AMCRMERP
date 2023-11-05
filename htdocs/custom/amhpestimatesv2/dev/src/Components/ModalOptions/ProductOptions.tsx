import { Modal, Button } from 'antd';
import './styles.css';

type ProductOptionsProps = {
    visible: boolean
    handleImpactProduct: () => void
    handleHardware: () => void
    handleMaterial: () => void
    handleDesign: () => void
    handleCancel: () => void
}

const ProductOptions = (p: ProductOptionsProps) => {
    return (
        <Modal
            okButtonProps={{ style: { display: 'none' } }}
            title="Product Type"
            visible={p.visible} 
            onCancel={p.handleCancel}>
            <div className='buttons_grid'>
                <Button className='button_product' onClick={p.handleImpactProduct}>Impact Product</Button>
                <Button className='button_product' onClick={p.handleHardware}>Hardware</Button>
                <Button className='button_product' onClick={p.handleMaterial}>Material</Button>
                <Button className='button_product' onClick={p.handleDesign}>Design</Button>
            </div>
        </Modal>
    );
};

export default ProductOptions;