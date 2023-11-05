import React from 'react';
import { Select } from 'antd';

export interface AutoCompleteData {
  value: string
  label?: string
}

export type AutoCompleteProps = {
  options: AutoCompleteData[]
  placeholder?: string
  onSelect?: (id:string) => void
}

const AutoCompleteComponent: React.FC<AutoCompleteProps> = ({options, placeholder, onSelect}) => {
  let fixedOptions = options?options.map(o => o.label ? o : {...o, label: o.value}):[]
  return (
    <Select
        showSearch
        style={{ width: "100%" }}
        placeholder={placeholder ?? "Search name"}
        optionFilterProp="label"
        onSelect={onSelect}
        options={fixedOptions}
        filterOption={true}
    />
  );
};

export default AutoCompleteComponent;