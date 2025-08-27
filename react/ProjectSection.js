const ProjectSection = () => {
  return (
    <section className="bg-[#0E1B4D] py-20 px-6">
      <div className="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center text-white">
        
        {/* === Card Properti === */}
        <div className="flex justify-center">
          <div className="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-sm">
            {/* Gambar Properti */}
            <img
              src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c"
              alt="Project Baru"
              className="w-full h-60 object-cover"
            />

            {/* Detail Properti */}
            <div className="p-5">
              <h3 className="font-semibold text-lg text-gray-900 leading-snug">
                The First Stone Gading Serpong Cluster Emerald
              </h3>
              <p className="text-sm text-gray-700 mt-2 font-medium">
                Rp 25 Miliar
              </p>
              <p className="text-xs text-gray-500">Tangerang</p>
            </div>

            {/* Tombol Aksi */}
            <div className="flex justify-between items-center px-5 py-4 border-t border-gray-200">
              <button className="text-green-600 flex items-center gap-2 text-sm font-medium hover:underline">
                <i className="fab fa-whatsapp"></i> WhatsApp
              </button>
              <button className="text-gray-700 text-sm font-medium hover:underline">
                E-Brochure
              </button>
            </div>
          </div>
        </div>

        {/* === Deskripsi Project === */}
        <div className="flex flex-col justify-center">
          <h2 className="text-3xl md:text-4xl font-bold mb-2">Project Terbaru</h2>
          <p className="text-blue-200 text-sm mb-4 tracking-wide uppercase">
            A Masterpiece in Modern Living
          </p>
          <p className="text-gray-200 leading-relaxed mb-6">
            Dengan lokasi strategis dan fasilitas bertaraf dunia, proyek terbaru kami bukan sekadar hunian, 
            melainkan sebuah karya seni arsitektur. Rasakan pengalaman tinggal di lingkungan mewah 
            yang hanya dimiliki oleh segelintir orang terpilih.
          </p>
          <button className="bg-gradient-to-r from-[#3C4CAC] to-[#2A3990] text-white px-6 py-3 rounded-lg w-fit font-medium hover:opacity-90 transition">
            Lihat Properti
          </button>
        </div>

      </div>
    </section>
  );
};
