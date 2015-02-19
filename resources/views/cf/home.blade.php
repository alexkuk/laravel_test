@extends('app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Stored transactions (sorted by timePlaced descending)</div>

				<div class="panel-body">
                    <?php echo $paginatorHtml ?>

                    <?php /** @var App\Cf\Model\Trade\Transaction $transactions */ ?>
                    <?php if ($transactions->total() > 0): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>From Currency</th>
                                    <th>To Currency</th>
                                    <th>Amount sold</th>
                                    <th>Amount bought</th>
                                    <th>Rate</th>
                                    <th>Time placed</th>
                                    <th>Country</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td><?php echo $transaction->userId ?></td>
                                        <td><?php echo $transaction->currencyFrom ?></td>
                                        <td><?php echo $transaction->currencyTo ?></td>
                                        <td><?php echo number_format($transaction->amountSell, 2) ?></td>
                                        <td><?php echo number_format($transaction->amountBuy, 2) ?></td>
                                        <td><?php echo number_format($transaction->rate, 4) ?></td>
                                        <td><?php echo date('F j, Y, g:i a', $transaction->timePlaced) ?></td>
                                        <td><?php echo $transaction->originatingCountry ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>

                        <?php echo $paginatorHtml ?>
                    <?php else: ?>
                        <p>No transactions registered</p>
                    <?php endif ?>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
