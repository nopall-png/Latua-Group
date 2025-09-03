const PropertyCard = ({ id, title, price, province, regency, property_type, image_url }) => {
  const goToDetail = () => {
    window.location.href = `/LatuaGroup/pages/detail_property.php?id=${id}`;
  };

  return (
    <div 
      className="min-w-[250px] max-w-[300px] bg-white rounded-lg shadow-md overflow-hidden cursor-pointer hover:shadow-lg transition"
      onClick={goToDetail}
    >
      <img 
        src={image_url || "/LatuaGroup/uploads/default.jpg"} 
        alt={title} 
        className="w-full h-40 object-cover" 
        onError={(e) => e.target.src = "/LatuaGroup/uploads/default.jpg"}
      />
      <div className="p-4">
        <h3 className="font-semibold text-lg">{title}</h3>
        <p className="text-gray-500 text-sm">{province}, {regency}</p>
        <p className="text-gray-800 font-bold">Rp {Number(price).toLocaleString("id-ID")}</p>
        <p className="text-xs text-gray-500 mt-1">
          {property_type === "for_sale" ? "Dijual" : "Disewa"}
        </p>
      </div>
    </div>
  );
};
