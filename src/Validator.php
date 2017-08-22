<?php

namespace Imjoehaines\Flowder\Validator;

final class Validator
{
    /**
     * Validates the given array of data
     *
     * @param string $table
     * @param array $data
     * @return Result
     */
    public function validate($table, array $data)
    {
        $expectedColumns = array_keys(reset($data));

        $badColumns = array_reduce(
            array_keys($data),
            $this->validateColumns($expectedColumns, $table, $data),
            []
        );

        if (!empty($badColumns)) {
            return Result::invalid($badColumns);
        }

        return Result::valid();
    }

    private function validateColumns(array $expectedColumns, $table, array $data)
    {
        return function (array $badColumns, $index) use ($expectedColumns, $table, $data) {
            $columns = array_keys($data[$index]);

            // use array_diff with array_keys instead of array_diff_key because
            // the latter still returns values and we want the keys returned
            $rowExtraColumns = array_diff($columns, $expectedColumns);

            if (!empty($rowExtraColumns)) {
                $badColumns[] = sprintf(
                    '"%s" — Fixture at index %d has extra columns: "%s"',
                    $table,
                    $index,
                    implode('", "', $rowExtraColumns)
                );
            }

            $rowMissingColumns = array_diff($expectedColumns, $columns);

            if (!empty($rowMissingColumns)) {
                $badColumns[] = sprintf(
                    '"%s" — Fixture at index %d is missing columns: "%s"',
                    $table,
                    $index,
                    implode('", "', $rowMissingColumns)
                );
            }

            return $badColumns;
        };
    }
}
