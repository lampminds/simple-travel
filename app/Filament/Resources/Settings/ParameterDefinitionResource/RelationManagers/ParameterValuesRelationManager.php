<?php

namespace App\Filament\Resources\Settings\ParameterDefinitionResource\RelationManagers;

use App\Models\ParameterValue;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ParameterValuesRelationManager extends RelationManager
{
    protected static string $relationship = 'parameterValues';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return (string) __('filament.resources.parameter_values');
    }

    public function form(Schema $schema): Schema
    {
        $owner = $this->getOwnerRecord();
        $isTenant = $owner->scope === 'tenant';

        $fields = [];
        if ($isTenant) {
            $fields[] = Select::make('account_id')
                ->label(__('filament.resources.parameter_value_fields.account_id'))
                ->relationship('account', 'name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('name'))
                ->searchable()
                ->preload()
                ->required();
        }

        $fields[] = Textarea::make('value')
            ->label(__('filament.resources.parameter_value_fields.value'))
            ->rows(6)
            ->nullable()
            ->columnSpanFull();

        return $this->defaultForm($schema->schema([
            Section::make('')->schema($fields)->columns($isTenant ? 2 : 1),
        ]));
    }

    public function table(Table $table): Table
    {
        $owner = $this->getOwnerRecord();
        $isTenant = $owner->scope === 'tenant';

        return $table
            ->recordActionsPosition(RecordActionsPosition::BeforeColumns)
            ->columns([
                TextColumn::make('account.name')
                    ->label(__('filament.resources.parameter_value_columns.account'))
                    ->visible($isTenant)
                    ->sortable(),
                TextColumn::make('value')
                    ->label(__('filament.resources.parameter_value_columns.value'))
                    ->limit(80)
                    ->wrap(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('filament-actions::create.single.modal.actions.create.label'))
                    ->visible(fn (): bool => $this->getOwnerRecord()->scope === 'tenant'
                        || $this->getOwnerRecord()->parameterValues()->count() === 0)
                    ->using(function (array $data): Model {
                        $owner = $this->getOwnerRecord();
                        $data['parameter_definition_id'] = $owner->id;
                        if ($owner->scope === 'system') {
                            $data['account_id'] = null;
                        }
                        $accountId = isset($data['account_id']) && $data['account_id'] !== '' && $data['account_id'] !== null
                            ? (int) $data['account_id']
                            : null;
                        ParameterValue::assertUniqueCombination($owner->id, $accountId);

                        return $this->getRelationship()->create($data);
                    }),
            ])
            ->recordActions(
                ActionGroup::make([
                    EditAction::make()
                        ->using(function (array $data, Model $record): void {
                            $owner = $this->getOwnerRecord();
                            if ($owner->scope === 'system') {
                                $data['account_id'] = null;
                            }
                            $accountId = isset($data['account_id']) && $data['account_id'] !== '' && $data['account_id'] !== null
                                ? (int) $data['account_id']
                                : null;
                            ParameterValue::assertUniqueCombination($owner->id, $accountId, $record->id);
                            $record->update($data);
                        }),
                    DeleteAction::make(),
                ])
            )
            ->modifyQueryUsing(fn (Builder $query) => $query->with('account'))
            ->defaultSort('id', 'desc');
    }
}
