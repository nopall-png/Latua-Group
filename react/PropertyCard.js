const PropertyCard = ({ id, title, price, province, regency, property_type, main_image_path, description }) => {
  const imageUrl = main_image_path 
    ? `/LatuaGroup/Uploads/properties/${main_image_path}` 
    : "/LatuaGroup/Uploads/default.jpg";

  return (
    <div className="bg-white rounded-2xl shadow-md overflow-hidden flex flex-col">
      {/* Image */}
      <a href={`/LatuaGroup/pages/detail_property.php?id=${id}`} className="block">
        <img src={imageUrl} alt={title} className="w-full h-48 object-cover rounded-t-2xl" />
      </a>

      {/* Content */}
      <div className="p-4 flex flex-col flex-grow">
        <h3 className="font-bold text-lg uppercase">{title}</h3>
        <p className="text-gray-500 text-sm flex items-center mb-1">
          <i className="fas fa-map-marker-alt mr-1 text-blue-600"></i>
          {regency}, {province}
        </p>

        {/* Price + Tag */}
        <div className="flex items-center justify-between mb-2">
          <p className="text-gray-900 font-bold">
            Rp {parseInt(price).toLocaleString("id-ID")}
          </p>
          <span className={`px-3 py-1 text-xs font-semibold rounded-full text-white 
            ${property_type === "for_sale" ? "bg-blue-800" : "bg-blue-500"}`}>
            {property_type === "for_sale" ? "JUAL" : "SEWA"}
          </span>
        </div>

        {/* Description */}
        <p className="text-gray-600 text-sm line-clamp-2">
          {description || "Deskripsi properti belum tersedia."}
        </p>
      </div>
    </div>
  );
};
