<?php

include 'Ofx.php';

$ofx = new Ofx('NU_17063927_01JAN2019_18FEV2021.ofx');
$balance = $ofx->getBalance();

?>
<html>
    <head>
        <title>Transações</title>
    </head>
    <body>
        <h1>Seu saldo em <?php echo date("d/m/Y", strtotime($balance['date'])); ?> é de R$ <?echo $balance['balance']; ?></h1>
        <h2>Transações</h2>
        
        <table border="1" cellpadding="3" cellspacing="0">
            <thead>
                <tr>
                    <th>Data</th>                    
                    <th>Descrição</th>                    
                    <th>Tipo</th>                    
                    <th>Valor</th>                
                </tr>            
            </thead>            
            <tbody>                
                <?php foreach ($ofx->getTransactions() as $transaction) : ?>
                    <tr> 
                        <td><?php echo date("Y-m-d", strtotime(substr($transaction->DTPOSTED, 0, 8))); ?></td>
                        <td><?php echo $transaction->MEMO; ?></td>
                        <td><?php echo $transaction->TRNTYPE; ?></td>
                        <td><?php echo $transaction->TRNAMT; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
</html>