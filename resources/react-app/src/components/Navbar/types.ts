import { DropdownProp } from "./Dropdown/types";

export interface NavbarProp {
	items: DropdownProp[]
}

export interface NavbarItemsResponse {
	message: string
	items: {
		[key: string]: {
			[key: string]: string[]
		}
	}
}
