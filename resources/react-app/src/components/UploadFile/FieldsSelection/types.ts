export interface FieldSelectionProp {
  upload_type: string,
  fields: {
    label: string, 
    field_name: string, 
    updatable: boolean,
    required: boolean
  }[],
  update: {
    [key: string]: string
  }[]
}
