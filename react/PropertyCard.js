const PropertyCard = ({ id, title, price, province, regency, property_type, image_url, description }) => {
  const imageUrl = image_url || "/LatuaGroup/uploads/default.jpg";

  const goToDetail = () => {
    window.location.href = `/LatuaGroup/pages/detail_property.php?id=${id}`;
  };

  return (
    <div 
      className="bg-white rounded-2xl shadow-md overflow-hidden flex flex-col cursor-pointer"
      onClick={goToDetail}
    >
      <img 
        src={imageUrl} 
        alt={title} 
        className="w-full h-48 object-cover rounded-t-2xl"
        onError={(e) => e.target.src = "/LatuaGroup/uploads/default.jpg"}
      />
      <div className="p-4 flex flex-col flex-grow">
        <h3 className="font-bold text-lg uppercase">{title}</h3>
        <p className="text-gray-500 text-sm mb-1">
          <i className="fas fa-map-marker-alt mr-1 text-blue-600"></i>
          {regency}, {province}
        </p>
        <div className="flex items-center justify-between mb-2">
          <p className="text-gray-900 font-bold">
            Rp {parseInt(price).toLocaleString("id-ID")}
          </p>
          <span className={`px-3 py-1 text-xs font-semibold rounded-full text-white 
            ${property_type === "for_sale" ? "bg-blue-800" : "bg-blue-500"}`}>
            {property_type === "for_sale" ? "JUAL" : "SEWA"}
          </span>
        </div>
        <p className="text-gray-600 text-sm line-clamp-2">
          {description || "Deskripsi properti belum tersedia."}
        </p>
      </div>
    </div>
  );
};
  