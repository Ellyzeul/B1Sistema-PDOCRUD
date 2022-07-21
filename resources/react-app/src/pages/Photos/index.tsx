import "./style.css"

export const PhotosPage = () => {
  return (
    <div className="photos-form">
      <form>
        <div>
          <label htmlFor="photo-name">Nome da foto</label>
          <input type="text" name="photo-name" id="photo-name" />
        </div>
        <div>
          <label htmlFor="photo-image">Foto a ser postada</label>
          <input 
            type="file" 
            name="photo-image"
            id="photo-image"
            accept="image/jpeg, image/jpg, image/png"
          />
        </div>
      </form>
    </div>
  )
}