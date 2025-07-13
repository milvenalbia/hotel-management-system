<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>

        @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 1000px;
            margin: 0 auto;
            background-color: #191c24;
            padding: 20px;
            border-radius: 10px;
            justify-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #191c24;
            color: #ffffff;
            text-align: center;
            padding: 10px;
            border-radius: 5px 5px 0 0;
        }

        .content {
            margin-left: 35px;
            width: 900px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px 5px 5px 5px;
        }

        .content p{
            margin-left: 40px;
            font-size: 16px;
            
        }

        .content h2{
            margin-left: 264px;
            margin-bottom: 30px;
            font-size: 30px;
            
        }

        .footer {
            background-color: #191c24;
            color: #ffffff;
            text-align: center;
            padding: 10px;
            border-radius: 0 0 5px 5px;
        }

        .d-flex{
            display: flex;
        }

        .text-right{
            text-align: right;
        }

        .text-sm{
            font-size: 0.900rem;
        }

        .table-bordered {
        border: 1px solid #2c2e33; }
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #525b72; }

        .table{
        width: 100%;
        margin-bottom: 1rem;
        color: #000000; }
        .table th,
        .table td{
            padding: 0.700rem;
            vertical-align: top;
            border: 1px solid #2c2e33;}
        .table thead th{
            vertical-align: bottom;
            border-bottom: 2px solid #2c2e33; }
        .table tbody{
            border-top: 2px solid #2c2e33; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                {{auth()->user()->hotel_name}}
            </h1>
        </div>


        <div class="content">
            <div class="d-flex">
                <h2 style="margin-top: 50px; margin-right: 250px; margin-left: 35px;">{{auth()->user()->hotel_name}}</h2>
                <div class="text-right">
                    <p class="text-small" style="margin-bottom: 0px;">Date: {{$bookingData['today']}}</p>
                    <p class="text-small" style="margin-bottom: 0px;">Receptionist: {{auth()->user()->name}}</p>
                </div>
            </div>
                <p class="text-small"><strong>Hotel Name: {{auth()->user()->hotel_name}}</strong></p>
                <p class="text-small">Email Address: 'hotel.ms.simulator@gmail.com'</p>
                <p class="text-small">Address: Marcelo, M.H Del Pilar St, Tagoloan, Misamis Oriental</p>
                <h3 class="text-small text-center">
                    <center><strong>Booking Information</strong></center>
                </h3>

                <table style="margin-top: 16px; width: 400px; margin-left: 45px;">
                    <tbody>
                        <tr>
                            <td>Name:</td>
                            <td>{{$bookingData['firstname']}} {{$bookingData['lastname']}}</td>
                        </tr>
                        <tr>
                            <td>Phone #:</td>
                            <td>{{$bookingData['phone']}}</td>
                        </tr>
                        <tr>
                            <td>Email Address:</td>
                            <td>{{$bookingData['email']}}</td>
                        </tr>
                        <tr>
                            <td>Adult:</td>
                            <td>{{$bookingData['adult']}}</td>
                        </tr>
                        <tr>
                            <td>Children:</td>
                            <td>{{$bookingData['children']}}</td>
                        </tr>
                        <tr>
                            <td>Room Type:</td>
                            <td>{{$bookingData['roomtype']}}</td>
                        </tr>
                        <tr>
                            <td>Check In Date:</td>
                            <td>{{$bookingData['check_in']}}</td>
                        </tr>
                        <tr>
                            <td>Check Out Date:</td>
                            <td>{{$bookingData['check_out']}}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="table table-bordered" style="margin-top: 16px; margin-left: 45px; width: 92%;" >
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Room</td>
                            <td>{{$bookingData['room']}}</td>
                            <td>&#8369;{{$bookingData['room_price']}}</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="font-weight: bold">Additonal Charges</td>
                        </tr>
                        <tr>
                            <td>Extra Bed</td>
                            <td>{{$bookingData['extra_bed']}}</td>
                            <td>&#8369;{{number_format($bookingData['extra_bed_amount'], 2, '.', ',')}}</td>
                        </tr>
                        <tr>
                            <td>Number of Nights</td>
                            <td>{{$bookingData['stay']}}</td>
                            <td>&#8369;{{$bookingData['nights_amount']}}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="font-weight: bold">Total</td>
                            <td style="font-weight: bold">&#8369;{{$bookingData['total_amount']}}</td>
                        </tr>
                    </tbody>
                </table>

                <center>
                    <h4 style="margin-top: 25px; width: 600px; text-align: center;">
                        " Thank you for choosing {{auth()->user()->hotel_name}}. We hope your stay was enjoyable. Enclosed is your detailed invoice.
                        Your satisfaction is our priority. If you have any feedback or suggestions, please let us know. We look forward to welcome you back in the future. "
                    </h4>
                </center>
        </div>


        <div class="footer">
            <p>&copy; 2023 Your {{auth()->user()->hotel_name}} & Resort. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
  