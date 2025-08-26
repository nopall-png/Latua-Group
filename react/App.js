const App = () => {
  return (
    <div className="w-full">
      {/* Properti Terbaru */}
      <section className="py-12 px-6">
        <PropertyList />
      </section>

      {/* Project Terbaru */}
      <section className="py-12 px-6 bg-white">
        <ProjectSection />
      </section>

      {/* Kenapa Memilih Kami */}
      <section className="py-12 px-6 bg-gray-50">
        <WhyChooseUsSection />
      </section>
    </div>
  );
};
