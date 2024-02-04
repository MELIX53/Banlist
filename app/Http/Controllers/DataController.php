<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    const DEFAULT_RESPONSE = [
        'success' => false,
        'error_message' => null,
        'response' => [
            'message' => '',
        ]
    ];

    const SUCCESS = 'success';
    const ERROR_MESSAGE = 'error_message';
    const RESPONSE = 'response';

    public function getAllPunishments(Request $request): JsonResponse
    {
        $response = self::DEFAULT_RESPONSE;
        try {
            $builders = [];

            $filters = $request->get('tables');
            $nick = str_replace(" ", "", ($request->get('nick') ?? ""));
            $punishedName = str_replace(" ", "", ($request->get('punishedName') ?? ""));
            $id = $request->get('id') ?? null;

            $tables = [
                'table_bans' => [
                    'raws' => ['clientID'],
                    'filterType' => 'lock_nick'
                ],

                'table_kicks' => [
                    'raws' => ['clientID', 'pardoned', 'timeLocking'],
                    'filterType' => 'kick'
                ],

                'table_mutes' => [
                    'raws' => ['clientID'],
                    'filterType' => 'lock_chat'
                ],

                'table_bans_os' => [
                    'raws' => [],
                    'filterType' => 'lock_os'
                ],
            ];

            foreach ($tables as $tableName => $tableData) {
                if ((bool)(($filters[$tableData['filterType']]) ?? false)) {
                    $defaultSelect = ['id', 'opponentName', 'clientID', 'punishedName', 'reason', 'pardoned', 'url', 'timeGenerated', 'timeLocking', 'confirmed', 'port'];
                    foreach ($tableData['raws'] as $raw) {
                        $defaultSelect[array_search($raw, $defaultSelect)] = 'NULL as ' . $raw;
                    }

                    $table = DB::table($tableName);
                    foreach ($defaultSelect as $row) {
                        $table->selectRaw($row);
                    }
                    $table->selectRaw("'" . $tableData['filterType'] . "' AS type");

                    $selectRawPrefix = 'CASE ';
                    $selectPorts = [];
                    foreach (config('settings.ports') as $port => $portData) {
                        $selectRawPrefix .= "WHEN port = {$port} THEN '{$portData['prefix']}' ";
                        if (isset($filters[$port]) and $filters[$port]) {
                            $selectPorts[] = $port;
                        }
                    }
                    $table->whereIn('port', $selectPorts);

                    $selectRawPrefix .= "END AS portPrefix";
                    $table->selectRaw($selectRawPrefix);

                    if ($id !== null) {
                        $table->whereRaw('id = ?', [$id]);
                    }

                    if (mb_strlen($punishedName) > 0) {
                        $table->whereRaw('LOWER(punishedName) = LOWER(?)', [$punishedName]);
                    }

                    if (mb_strlen($nick) > 0) {
                        $table->selectRaw("LENGTH(opponentName) - LENGTH('{$nick}') AS similarity");
                        $table->where('opponentName', 'like', $nick . "%");
                    }

                    $builders[$tableName] = $table;
                    unset($table);
                }
            }

            $countsRecords = [];
            $allCount = 0;
            foreach ($builders as $tableName => $builder) {
                if ($builder !== null) {
                    $count = $builder->count();
                    $allCount += $count;
                    $countsRecords[str_replace('table_', 'count_', $tableName)] = $count;
                    unset($builder);
                }
            }
            $countsRecords['all'] = $allCount;

            $collection = null;
            foreach ($builders as $builder) {
                if ($collection == null) {
                    $collection = $builder;
                    continue;
                }
                $collection = $collection->union($builder);
            }

            if ($collection == null) {
                $response[self::ERROR_MESSAGE] = 'Ничего не найдено!';
                return response()->json($response);
            }

            if (mb_strlen($nick) > 0) {
                $collection = $collection->orderBy('similarity');
            }

            $collection = $collection->orderBy('timeGenerated', 'desc');
            $collection = $collection->get();

            $page = $request->get('page') ?? 1;
            $perPage = $request->get('perPage') ?? 10;

            if ($perPage > 15) {
                $response[self::ERROR_MESSAGE] = 'Можно запрашивать не больше 15 постов!';
                return response()->json($response);
            }

            $countPages = (int)ceil($collection->count() / $perPage);
            $data = $collection->forPage($page, $perPage);

            $response[self::SUCCESS] = !(count($data) == 0);
            $response[self::ERROR_MESSAGE] = ((count($data) == 0 ? "Ничего не найдено" : null));
            $response[self::RESPONSE] = ((count($data) == 0 ? null : ['countPages' => $countPages, 'countRecords' => $countsRecords, 'data' => $data]));

            return response()->json($response);
        } catch (\Exception $errorMessage) {
            $response[self::ERROR_MESSAGE] = 'Допущена ошибка!';
            $response[self::RESPONSE]['message'] = $errorMessage;
            return response()->json($response);
        }
    }
}
