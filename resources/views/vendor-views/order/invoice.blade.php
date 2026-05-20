@php
    $bjPrintOnly = request('print') == '1';
@endphp

@if($bjPrintOnly)
    <!DOCTYPE html>
    <html dir="rtl" lang="ar">
    <head>
        <meta charset="utf-8">
        <title>{{ translate('messages.Order_Invoice') }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>html,body{margin:0;padding:0;background:#fff;}</style>
    </head>
    <body>
        @include('new_invoice')
        <script>
            // Auto-print on load, kiosk-printing skips the dialog.
            window.addEventListener('load', function(){
                setTimeout(function(){ try { window.print(); } catch(e){} }, 300);
            });
        </script>
    </body>
    </html>
@else
    @extends('layouts.vendor.app')

    @section('title', translate('messages.Order_Invoice'))

    @section('content')
        @include('new_invoice')
    @endsection
@endif
