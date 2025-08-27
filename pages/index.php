<?php
// Prevent caching
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Latuae Land</title>
  
  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- React & Babel -->
  <script crossorigin src="https://unpkg.com/react@18/umd/react.development.js"></script>
  <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
  <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    /* Hapus scrollbar horizontal */
    .scrollbar-hide::-webkit-scrollbar {
      display: none;
    }
    .scrollbar-hide {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }

    /* Line clamp = potong teks jadi max 2 baris */
    .line-clamp-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
  </style>
</head>
<body class="bg-gray-100 font-['Raleway'] text-black overflow-x-hidden">

  <!-- React Root -->
  <div id="root"></div>

  <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/LatuaGroup/includes/footer.php'; ?>

  <!-- React Components (pastikan file2 ada di folder react/) -->
  <script type="text/babel" src="/LatuaGroup/react/HeroSearch.js"></script>
  <script type="text/babel" src="/LatuaGroup/react/PropertyCard.js"></script>
  <script type="text/babel" src="/LatuaGroup/react/PropertyList.js"></script>
  <script type="text/babel" src="/LatuaGroup/react/BankPartnerSection.js"></script>
  <script type="text/babel" src="/LatuaGroup/react/ProjectSection.js"></script>
  <script type="text/babel" src="/LatuaGroup/react/WhyChooseUsSection.js"></script>
  <script type="text/babel" src="/LatuaGroup/react/App.js"></script>

  <!-- Render React App -->
  <script type="text/babel">
    ReactDOM.createRoot(document.getElementById("root")).render(<App />);
  </script>
</body>
</html>
