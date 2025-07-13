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
                <h2 style="margin-top: 50px; margin-right: 250px; margin-left: 35px;">{{auth()->user()->hotel_name}} Invoice</h2>
                <div class="text-right">
                    <p class="text-small" style="margin-bottom: 0px;">Date: {{$other_data['today']}}</p>
                    <p class="text-small" style="margin-bottom: 0px;">Receptionist: {{auth()->user()->name}}</p>
                    <p class="text-small" style="margin-bottom: 0px;">Invoice #: {{$other_data['invoice_no']}}</p>
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
                        @foreach($booking_data as $b_data)
                        <tr>
                            <td>Name:</td>
                            <td>{{$b_data['firstname']}} {{$b_data['lastname']}}</td>
                        </tr>
                        <tr>
                            <td>Phone #:</td>
                            <td>{{$b_data['phone']}}</td>
                        </tr>
                        <tr>
                            <td>Email Address:</td>
                            <td>{{$b_data['email']}}</td>
                        </tr>
                        <tr>
                            <td>Room Type:</td>
                            <td>{{$b_data['roomtype']}}</td>
                        </tr>
                        <tr>
                            <td>Check In Date:</td>
                            <td>{{$other_data['check_in']}}</td>
                        </tr>
                        <tr>
                            <td>Check Out Date:</td>
                            <td>{{$other_data['check_out']}}</td>
                        </tr>
                        @endforeach
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
                        @foreach($booking_data as $b_data)
                        <tr>
                            <td>Room</td>
                            <td>{{$b_data['room']}}</td>
                            <td>&#8369;{{number_format($other_data['room_price'], 2, '.', ',')}}</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="font-weight: bold">Additonal Charges</td>
                        </tr>
                        <tr>
                            <td>Extra Bed</td>
                            <td>{{$b_data['extra_bed']}}</td>
                            <td>&#8369;{{number_format($b_data['extra_bed_amount'], 2, '.', ',')}}</td>
                        </tr>
                        <tr>
                            <td>Extend Hours</td>
                            <td>{{$b_data['extend_hours']}}</td>
                            <td>&#8369;{{number_format($other_data['extend_hours_amount'], 2, '.', ',')}}</td>
                        </tr>
                        <tr>
                            <td>Extend Days</td>
                            <td>{{$b_data['extend_days']}}</td>
                            <td>&#8369;{{number_format($other_data['extend_days_amount'], 2, '.', ',')}}</td>
                        </tr>
                        <tr>
                            <td>Number of Nights</td>
                            <td>{{$other_data['nights']}}</td>
                            <td>&#8369;{{number_format($other_data['nights_amount'], 2, '.', ',')}}</td>
                        </tr>
                        <tr>
                            <td>Total Nights</td>
                            <td>{{$other_data['total_nights']}}</td>
                            <td>&#8369;{{number_format($other_data['total_nights_amount'], 2, '.', ',')}}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="font-weight: bold">Sub Total</td>
                            <td style="font-weight: bold">&#8369;{{number_format($other_data['book_sub_total'], 2, '.', ',')}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <h3 class="text-small" style="margin-bottom: 14px; margin-top: 30px; margin-left: 45px;">
                   <strong>Dining Charges</strong>
                </h3>
                <table class="table table-bordered" style="margin-top: 16px; margin-left: 45px; width: 92%;" >
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Date Purchase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($dining_data) > 0)
                                @php
                                    $prevOrderId = null;
                                @endphp
                            
                            @foreach (collect($dining_data)->groupBy('order_id') as $d_items)
                                @foreach ($d_items as $item)
                                    @if($item['order']['id'] !== $prevOrderId)
                                        <tr>
                                            <td colspan="5">
                                                @if(isset($item['order']) && $item['order'] && $item['order']['created_at'])
                                                    @if($item['order']['created_at']->hour >= 5 && $item['order']['created_at']->hour < 12)
                                                        <h4>Breakfast</h4>
                                                    @elseif ($item['order']['created_at']->hour >= 12 && $item['order']['created_at']->hour < 18)
                                                        <h4>Lunch</h4>
                                                    @else
                                                        <h4>Dinner</h4>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                            
                                    <tr>
                                        <td>{{$item['product_name']}}</td>
                                        <td>&#8369;{{number_format($item['price'], 2, '.',',')}}</td>
                                        <td>{{$item['quantity']}}</td>
                                        <td>&#8369;{{number_format($item['total'], 2, '.',',')}}</td>
                                        <td>{{$item['date_created']}}</td>
                                    </tr>
                            
                                    @php
                                        $prevOrderId = $item['order']['id'];
                                    @endphp
                                @endforeach
                            @endforeach
                    
                        @else
                            <tr>
                                <td class="text-center" colspan="5">No record found</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="3"></td>
                            <td style="font-weight: bold">Sub Total</td>
                            <td style="font-weight: bold">&#8369;{{number_format($other_data['dine_sub_total'], 2, '.', ',')}}</td>
                        </tr>
                    </tbody>
                </table>

                <table style="margin-top: 16px; width: 400px; margin-left: 45px;">
                    <tbody>
                        <tr>
                            <td>Booking Sub Total:</td>
                            <td>&#8369;{{number_format($other_data['book_sub_total'], 2, '.', ',')}}</td>
                        </tr>
                        <tr>
                            <td>Dining Sub Total:</td>
                            <td>&#8369;{{number_format($other_data['dine_sub_total'], 2, '.', ',')}}</td>
                        </tr>
                        <tr>
                            <td>Cash Amount:</td>
                            <td>&#8369;{{number_format($other_data['cash_amount'], 2, '.', ',')}}</td>
                        </tr>
                        <tr>
                            <td style="text-transform: uppercase; font-size: 20px; font-weight: bold;">Total Amount:</td>
                            <td style="text-transform: uppercase; font-size: 20px; font-weight: bold;">&#8369;{{number_format($other_data['total_amount'], 2, '.', ',')}}</td>
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