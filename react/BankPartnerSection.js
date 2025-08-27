const BankPartnerSection = () => {
  const banks = [
    { id: 1, name: "Bank BCA", logo: "https://upload.wikimedia.org/wikipedia/commons/5/5a/BCA_logo.svg" },
    { id: 2, name: "Bank Mandiri", logo: "https://upload.wikimedia.org/wikipedia/commons/1/16/Bank_Mandiri_logo.svg" },
    { id: 3, name: "Bank BRI", logo: "https://upload.wikimedia.org/wikipedia/commons/1/11/Bank_Rakyat_Indonesia_logo.svg" },
    { id: 4, name: "Bank BTN", logo: "https://upload.wikimedia.org/wikipedia/commons/5/5d/Bank_BTN_logo.svg" },
  ];

  return (
    <section className="py-12 px-6 bg-gray-50">
      <div className="max-w-6xl mx-auto text-center">
        {/* Title */}
        <h2 className="text-2xl md:text-3xl font-bold text-gray-800 mb-8">
          KERJA SAMA BANK
        </h2>

        {/* Logo Grid */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-6 items-center justify-center">
          {banks.map((bank) => (    
            <div
              key={bank.id}
              className="flex items-center justify-center bg-white rounded-xl shadow-md p-4 hover:shadow-lg transition"
            >
              <img
                src={bank.logo}
                alt={bank.name}
                className="h-12 object-contain"
              />
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};
