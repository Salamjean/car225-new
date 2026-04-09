$results = DB::select("select date_voyage, statut, count(*) as count from reservations where date_voyage in ('2026-04-10', '2026-04-11') group by date_voyage, statut");
foreach($results as $row) {
    echo "Date: {$row->date_voyage} | Status: {$row->statut} | Count: {$row->count}" . PHP_EOL;
}
