import { createContext } from "react";
import { DropdownProp } from "../components/Navbar/Dropdown/types";

export const NavbarContext = createContext([
  {} as {[key: string]: DropdownProp[]}, 
  (prevState: {[key: string]: DropdownProp[]}): void => {}
])
