import React from 'react';
import { Modal, Button } from 'antd';
import './styles.css';

export enum RoomType {
    Unknown,
    Bedroom,
    Bathroom,
    Other
}

type RoomOptionsProps = {
    isModalVisible: boolean;
    handleCancel: () => void;
    handleTypeOptions?: (type:RoomType) => void;
}

const RoomOptions = ({ isModalVisible, handleCancel, handleTypeOptions }: RoomOptionsProps) => {
    return (
        <Modal
            okButtonProps={{ style: { display: 'none' } }}
            title="In what type of room is this opening located?"
            visible={isModalVisible} 
            onCancel={handleCancel}>
            <div className='buttons_grid'>
                <Button className='button_product' onClick={() => handleTypeOptions && handleTypeOptions(RoomType.Bedroom)}>Bedroom</Button>
                <Button className='button_product' onClick={() => handleTypeOptions && handleTypeOptions(RoomType.Bathroom)}>Bathroom</Button>
                <Button className='button_product' onClick={() => handleTypeOptions && handleTypeOptions(RoomType.Other)}>Other</Button>
                <Button className='button_product' onClick={() => handleTypeOptions && handleTypeOptions(RoomType.Unknown)}>I don't know</Button>
            </div>
        </Modal>
    );
};

export default RoomOptions;