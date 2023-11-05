import { FC } from 'react';
import { Checkbox } from 'antd';

function onChange(checkedValues: any) {
}

const options = [
    { label: 'Include Installation Services', value: 'installation' },
    { label: 'Alteration', value: 'alteration' }
];

const Checkboxes: FC = () => {
    return (
        <>
            <Checkbox.Group options={options} onChange={onChange} />
        </>

    );
};

export default Checkboxes;