const HeroSearch = () => {
  return (
    <div className="relative w-full h-[300px] md:h-[400px]">
      {/* Background */}
      <img
        src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c"
        alt="bg"
        className="absolute inset-0 w-full h-full object-cover"
      />
      <div className="absolute inset-0 bg-black/40"></div>

      {/* Search Box */}
      <div className="relative flex flex-col items-center justify-center h-full">
        <div className="bg-[#0E1B4D] rounded-2xl w-[85%] max-w-4xl shadow-lg">
          
          {/* Tabs */}
          <div className="flex text-white border-b border-white/30">
            <button className="px-6 py-3 text-sm font-semibold border-b-2 border-white">
              Properti Dijual
            </button>
            <button className="px-6 py-3 text-sm font-medium hover:border-b-2 hover:border-white/70">
              Properti Disewa
            </button>
          </div>

          {/* Input */}
          <div className="flex items-center px-4 py-4 gap-3">
            <div className="flex items-center bg-white rounded-md flex-1 px-3 shadow-sm">
              <i className="fas fa-search text-gray-400"></i>
              <input
                type="text"
                placeholder="Mau cari properti dimana?"
                className="flex-1 px-2 py-2 text-gray-700 focus:outline-none"
              />
            </div>
            <button className="bg-[#3C4CAC] text-white px-6 py-2 rounded-md hover:bg-[#2A3990] transition">
              Cari
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};
