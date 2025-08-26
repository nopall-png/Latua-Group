// WhyChooseUsSection.js

const WhyChooseUsSection = () => {
  return (
    <div className="max-w-6xl mx-auto py-16 px-6">
      {/* Judul */}
      <div className="text-center mb-12">
        <p className="text-gray-400 text-sm">Kami</p>
        <h2 className="text-2xl md:text-3xl font-bold">
          Kenapa Memilih Kami?
        </h2>
        <div className="w-24 h-1 bg-blue-600 mx-auto mt-2 rounded"></div>
      </div>

      {/* Konten */}
      <div className="grid md:grid-cols-2 gap-8 items-center">
        {/* Gambar */}
        <div className="flex justify-center">
          <img
            src="/LatuaGroup/uploads/team.PNG"
            alt="Tim Kami"
            className="rounded-lg shadow-lg max-h-72 object-cover"
          />
        </div>

        {/* Testimoni / alasan */}
        <div className="relative bg-gray-800 text-white p-6 rounded-lg shadow-lg">
          <div className="absolute -top-8 left-6 text-yellow-400 text-6xl font-bold">
            &quot;
          </div>
          <p className="relative z-10 leading-relaxed">
            Kami adalah perusahaan properti yang berkomitmen menghadirkan hunian premium dengan lokasi strategis, desain modern, serta fasilitas eksklusif yang mendukung gaya hidup berkelas. 
            Dengan pengalaman dan reputasi terpercaya, setiap proyek kami tidak hanya menjadi tempat tinggal nyaman bagi keluarga, 
            tetapi juga aset investasi bernilai tinggi di masa depan.
          </p>
        </div>
      </div>
    </div>
  );
};
