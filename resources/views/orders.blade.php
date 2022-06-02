<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/76e8507a5a.js" crossorigin="anonymous"></script>
    <title>B1Sistema</title>
</head>
<body onkeydown="closeModals(event)">
    {!! $pdocrud !!}
    <x-supplier-url-modal/>

    @stack('styles')
    @stack('scripts')
</body>
</html>
