// ProjectSection.js

const ProjectSection = () => {
  return (
    <div className="max-w-6xl mx-auto bg-[#0E1B4D] text-white rounded-lg shadow-lg overflow-hidden grid md:grid-cols-2">
      {/* Gambar */}
      <div className="flex items-center justify-center p-6">
        <div className="bg-white rounded-2xl overflow-hidden shadow-lg w-full max-w-md">
          <img
            src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c"
            alt="Project Baru"
            className="w-full h-56 object-cover rounded-t-2xl"
          />
          <div className="p-4">
            <h3 className="font-semibold text-black">
              The First Stone Gading Serpong Cluster Emerald
            </h3>
            <p className="text-sm text-gray-600 mt-1">Rp 25 Miliar</p>
            <p className="text-xs text-gray-500">Tangerang</p>
          </div>
          <div className="flex justify-between items-center px-4 py-3 border-t">
            <button className="text-green-600 flex items-center gap-2 text-sm">
              <i className="fab fa-whatsapp"></i> WhatsApp
            </button>
            <button className="text-gray-600 text-sm">E-Brochure</button>
          </div>
        </div>
      </div>

      {/* Deskripsi */}
      <div className="p-8 flex flex-col justify-center">
        <h2 className="text-2xl font-bold mb-2">Project terbaru</h2>
        <p className="text-gray-300 text-sm mb-4">A Masterpiece in Modern Living</p>
        <p className="text-gray-200 leading-relaxed mb-6">
          Dengan lokasi strategis dan fasilitas bertaraf dunia, proyek terbaru kami bukan sekadar hunian, 
          melainkan sebuah karya seni arsitektur. Rasakan pengalaman tinggal di lingkungan mewah 
          yang hanya dimiliki oleh segelintir orang terpilih.
        </p>
        <button className="bg-gradient-to-r from-[#3C4CAC] to-[#2A3990] text-white px-6 py-2 rounded-lg w-fit hover:opacity-90 transition">
          Liat Properti
        </button>
      </div>
    </div>
  );
};
