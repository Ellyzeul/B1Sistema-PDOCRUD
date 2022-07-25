import { MouseEventHandler } from "react"
import "./style.css"

export const PhotosSearchPage = () => {
  

  return (
    <div className="photos-search-page">
      <div className="search-container">
        <input className="photo-search" type="text" />
        <button>Pesquisar</button>
      </div>
      <div className="bottom-container">
        <p>Fotos pesquisadas</p>
        <div className="display-container">
          <img className="image-display" src="http://localhost:8000/static/photos/test_3.jpg" alt="" />
        </div>
      </div>
    </div>
  )
}
