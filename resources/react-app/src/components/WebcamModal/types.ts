export type WebcamModalProp = {
  is_open: boolean, 
  set_is_open: React.Dispatch<React.SetStateAction<boolean>>, 
  handle_image: (imageURL: string) => void, 
}
