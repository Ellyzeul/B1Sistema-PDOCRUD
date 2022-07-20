import "./style.css"

export const PhotosPage = () => {
  return (
    <div className="photos-form">
      <form>
        <div>
          <label htmlFor="photo-input">Foto a ser postada</label>
          <input 
            type="file" 
            name="photo-input"
            accept="image/jpeg, image/jpg, image/png"
          />
        </div>
      </form>
    </div>
  )
}