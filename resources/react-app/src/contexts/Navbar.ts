import { createContext } from "react";

export const NavbarContext = createContext([
  null as (JSX.Element | null), 
  (prevState: JSX.Element): void => {}
])
