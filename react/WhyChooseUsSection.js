const WhyChooseUsSection = () => {
  return (
    <section className="bg-white py-20 px-4 sm:px-6">
      <div className="max-w-6xl mx-auto relative">

        {/* === JUDUL === */}
        <div className="text-center mb-16">
          <p className="text-gray-500 text-lg">Kami</p>
          <h2 className="text-3xl md:text-4xl font-bold text-gray-800 mt-1">
            Kenapa Memilih Kami?
          </h2>
          <div className="w-24 h-1 bg-blue-600 mx-auto mt-4 rounded-full"></div>
        </div>

        {/* === KONTEN === */}
        <div className="relative flex flex-col md:flex-row items-center justify-center">
          
          {/* Foto */}
          <div className="relative z-20 w-full md:w-1/2 lg:w-5/12">
            <img
              src="/LatuaGroup/uploads/team.PNG"
              alt="Tim Lavera Land"
              className="rounded-xl shadow-2xl w-full h-[420px] object-cover"
            />
          </div>

          {/* Box teks */}
          <div className="relative z-10 w-full md:w-3/4 bg-[#1F2937] text-white rounded-xl shadow-2xl mt-[-50px] md:mt-0 md:ml-[-120px]">
            <div className="relative p-8 pt-16 md:p-12 lg:p-16">
              
              {/* Icon kutip */}
              <div className="absolute top-[-30px] left-6 md:left-[140px] text-yellow-400 text-[120px] font-black opacity-70 z-0">
                &rdquo;
              </div>

              {/* Wrapper teks untuk geser lebih ke kanan */}
              <div className="relative z-10 md:ml-40 lg:ml-56">
                <p className="text-lg leading-relaxed">
                  Kami adalah perusahaan properti yang berkomitmen menghadirkan hunian premium 
                  dengan lokasi strategis, desain modern, serta fasilitas eksklusif yang mendukung 
                  gaya hidup berkelas. Dengan pengalaman dan reputasi terpercaya, setiap proyek kami 
                  tidak hanya menjadi tempat tinggal nyaman bagi keluarga, tetapi juga aset investasi 
                  bernilai tinggi di masa depan.
                </p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>
  );
};
