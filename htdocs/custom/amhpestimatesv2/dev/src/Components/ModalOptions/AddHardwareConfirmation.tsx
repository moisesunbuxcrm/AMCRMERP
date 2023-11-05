import React from 'react';
import { Modal, Button } from 'antd';
import './styles.css';

type HardwareConfirmatioProps = {
    isModalVisible: boolean;
    onCancel: () => void;
    onYes?: () => void;
    onNo?: () => void;
}

const AddHardwareConfirmation = ({ isModalVisible, onYes, onNo, onCancel }: HardwareConfirmatioProps) => {
    return (
        <Modal
            okButtonProps={{ style: { display: 'none' } }}
            title="Do you want to include new hardware?"
            visible={isModalVisible} 
            onCancel={onCancel}>
            <div className='buttons_grid'>
                <Button className='button_product' onClick={() => onYes && onYes()}>Yes</Button>
                <Button className='button_product' onClick={() => onNo && onNo()}>No</Button>
                <Button className='button_product' onClick={() => onCancel && onCancel()}>Cancel</Button>
            </div>
        </Modal>
    );
};

export default AddHardwareConfirmation;