<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\PostResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\PostResource\RelationManagers\AuthorsRelationManager;
use App\Filament\Resources\PostResource\RelationManagers\CommentsRelationManager;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Blog';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Create a post')
                    ->description('Create post over here')
                    ->schema([
                        TextInput::make('title')->required()->minLength(1)->maxLength(150)
                        ->live(onBlur:true)
                        ->afterStateUpdated(function(string $operation,string $state,Forms\Set $set){
                            $set('slug',Str::slug($state));
                        }),
                        TextInput::make('slug')->unique(ignoreRecord: true)->required(),

                        Select::make('category_id')
                            ->label('Category')
                            // ->options(Category::all()->pluck('name', 'id'))
                            ->relationship('category', 'name')
                            ->required(),
                        ColorPicker::make('color')->required(),
                        MarkdownEditor::make('content')->required()->columnSpanFull(),
                    ])->columnSpan(2)->columns(2),

                Group::make()->schema([
                    Section::make('Image')
                        ->schema([
                            FileUpload::make('thumbnail')
                                ->disk('public')
                                ->directory('thumbnails'),
                        ])->collapsible(),
                    Section::make('meta')
                        ->schema([
                            TagsInput::make('tags')->required(),
                            Checkbox::make('published'),
                        ]),
                    // Section::make('authors')
                    // ->schema([
                    //     Select::make('authors')
                    //         ->label('Co authors')
                    //         ->relationship('authors', 'name')
                    //         ->multiple()
                    //         ->required(),
                    // ]),
                ])->columnSpan(1)


            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')->toggleable(),
                TextColumn::make('title')->toggleable()->searchable()->sortable(),
                TextColumn::make('slug')->toggleable()->sortable(),
                TextColumn::make('category.name')->toggleable()->searchable()->sortable(),
                ColorColumn::make('color')->toggleable(),
                TextColumn::make('tags')->toggleable()->searchable(),
                CheckboxColumn::make('published')->toggleable(),
            ])
            ->filters([
                // Filter::make('Published Posts')
                //     ->query(function (Builder $query): Builder {
                //         return $query->where('published', true);
                //     }),
                TernaryFilter::make('published'),
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AuthorsRelationManager::class,
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
