import React from "react";

const PropertyCard = ({ id, title, price, province, regency, property_type, image_url }) => {
  return (
    <div className="min-w-[250px] max-w-[300px] bg-white rounded-lg shadow-md overflow-hidden">
      <img src={image_url} alt={title} className="w-full h-40 object-cover" />
      <div className="p-4">
        <h3 className="font-semibold text-lg">{title}</h3>
        <p className="text-gray-500 text-sm">{province}, {regency}</p>
        <p className="text-gray-800 font-bold">Rp {Number(price).toLocaleString()}</p>
        <p className="text-xs text-gray-500 mt-1">
          {property_type === "for_sale" ? "Dijual" : "Disewa"}
        </p>
      </div>
    </div>
  );
};

const PropertyList = () => {
  const [properties, setProperties] = React.useState([]);
  const [loading, setLoading] = React.useState(true);

  React.useEffect(() => {
    fetch("/LatuaGroup/api/get_properties.php")
      .then(res => res.json())
      .then(data => {
        setProperties(data);
        setLoading(false);
      })
      .catch(err => {
        console.error("Error fetching properties:", err);
        setLoading(false);
      });
  }, []);

  if (loading) return <p className="text-center text-gray-500">Loading...</p>;

  return (
    <div className="relative">
      <h2 className="text-center text-gray-400 text-sm uppercase">Properti</h2>
      <h1 className="text-2xl font-bold text-center mb-6">Properti Terbaru</h1>

      {/* Slider */}
      <div id="property-slider" className="flex gap-6 overflow-x-auto px-6 pb-4 scrollbar-hide">
        {properties.length === 0 
          ? <p className="col-span-full text-center text-gray-500">Belum ada properti tersedia.</p>
          : properties.map((p) => <PropertyCard key={p.id} {...p} />)}
      </div>

      {/* Arrows */}
      <button 
        onClick={() => document.querySelector('#property-slider').scrollBy({ left: -300, behavior: 'smooth' })}
        className="absolute left-0 top-1/2 -translate-y-1/2 bg-white shadow p-2 rounded-full"
      >
        <i className="fas fa-chevron-left"></i>
      </button>
      <button 
        onClick={() => document.querySelector('#property-slider').scrollBy({ left: 300, behavior: 'smooth' })}
        className="absolute right-0 top-1/2 -translate-y-1/2 bg-white shadow p-2 rounded-full"
      >
        <i className="fas fa-chevron-right"></i>
      </button>
    </div>
  );
};

export default PropertyList;
