export type Json =
  | string
  | number
  | boolean
  | null
  | { [key: string]: Json | undefined }
  | Json[]

export type Database = {
  public: {
    Tables: {
      boutiques: {
        Row: {
          adresse: string | null
          code: string | null
          code_postal: string | null
          date_creation: string | null
          email: string | null
          facturation: string | null
          id: string
          is_active: boolean | null
          is_cgv: boolean | null
          n_rcs: string | null
          nom: string
          pays: string | null
          piece_identity: string | null
          siren: string | null
          telephone: string | null
          user_id: string | null
          ville: string | null
        }
        Insert: {
          adresse?: string | null
          code?: string | null
          code_postal?: string | null
          date_creation?: string | null
          email?: string | null
          facturation?: string | null
          id?: string
          is_active?: boolean | null
          is_cgv?: boolean | null
          n_rcs?: string | null
          nom: string
          pays?: string | null
          piece_identity?: string | null
          siren?: string | null
          telephone?: string | null
          user_id?: string | null
          ville?: string | null
        }
        Update: {
          adresse?: string | null
          code?: string | null
          code_postal?: string | null
          date_creation?: string | null
          email?: string | null
          facturation?: string | null
          id?: string
          is_active?: boolean | null
          is_cgv?: boolean | null
          n_rcs?: string | null
          nom?: string
          pays?: string | null
          piece_identity?: string | null
          siren?: string | null
          telephone?: string | null
          user_id?: string | null
          ville?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "boutiques_user_id_fkey"
            columns: ["user_id"]
            isOneToOne: false
            referencedRelation: "users"
            referencedColumns: ["id"]
          },
        ]
      }
      frais_service: {
        Row: {
          date: string | null
          id: string
          pourcentage: number
          pourcentage_boutique: number
          type: string
        }
        Insert: {
          date?: string | null
          id?: string
          pourcentage: number
          pourcentage_boutique: number
          type: string
        }
        Update: {
          date?: string | null
          id?: string
          pourcentage?: number
          pourcentage_boutique?: number
          type?: string
        }
        Relationships: []
      }
      frais_service_boutique: {
        Row: {
          boutique_id: string | null
          date: string | null
          id: string
          pourcentage: number
          type: string
        }
        Insert: {
          boutique_id?: string | null
          date?: string | null
          id?: string
          pourcentage: number
          type: string
        }
        Update: {
          boutique_id?: string | null
          date?: string | null
          id?: string
          pourcentage?: number
          type?: string
        }
        Relationships: [
          {
            foreignKeyName: "frais_service_boutique_boutique_id_fkey"
            columns: ["boutique_id"]
            isOneToOne: false
            referencedRelation: "boutiques"
            referencedColumns: ["id"]
          },
        ]
      }
      gencodes: {
        Row: {
          code: string
          id: string
          operateur: string
          valeur: string
        }
        Insert: {
          code: string
          id?: string
          operateur: string
          valeur: string
        }
        Update: {
          code?: string
          id?: string
          operateur?: string
          valeur?: string
        }
        Relationships: []
      }
      gestes_commerciaux: {
        Row: {
          admin_id: string | null
          boutique_id: string | null
          date: string | null
          id: string
          montant: number
          motif: string
        }
        Insert: {
          admin_id?: string | null
          boutique_id?: string | null
          date?: string | null
          id?: string
          montant: number
          motif: string
        }
        Update: {
          admin_id?: string | null
          boutique_id?: string | null
          date?: string | null
          id?: string
          montant?: number
          motif?: string
        }
        Relationships: [
          {
            foreignKeyName: "gestes_commerciaux_admin_id_fkey"
            columns: ["admin_id"]
            isOneToOne: false
            referencedRelation: "users"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "gestes_commerciaux_boutique_id_fkey"
            columns: ["boutique_id"]
            isOneToOne: false
            referencedRelation: "boutiques"
            referencedColumns: ["id"]
          },
        ]
      }
      notifications: {
        Row: {
          created_at: string | null
          id: string
          message: string
          status: string
          user_id: string | null
        }
        Insert: {
          created_at?: string | null
          id?: string
          message: string
          status: string
          user_id?: string | null
        }
        Update: {
          created_at?: string | null
          id?: string
          message?: string
          status?: string
          user_id?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "notifications_user_id_fkey"
            columns: ["user_id"]
            isOneToOne: false
            referencedRelation: "users"
            referencedColumns: ["id"]
          },
        ]
      }
      offres: {
        Row: {
          description: string
          devise: string
          id: string
          montant: number
          montant_devise: number | null
          nom: string
          operateur_id: string | null
          type_offre_id: string | null
        }
        Insert: {
          description: string
          devise: string
          id?: string
          montant: number
          montant_devise?: number | null
          nom: string
          operateur_id?: string | null
          type_offre_id?: string | null
        }
        Update: {
          description?: string
          devise?: string
          id?: string
          montant?: number
          montant_devise?: number | null
          nom?: string
          operateur_id?: string | null
          type_offre_id?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "offres_operateur_id_fkey"
            columns: ["operateur_id"]
            isOneToOne: false
            referencedRelation: "operateurs"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "offres_type_offre_id_fkey"
            columns: ["type_offre_id"]
            isOneToOne: false
            referencedRelation: "types_offres"
            referencedColumns: ["id"]
          },
        ]
      }
      operateurs: {
        Row: {
          actif: boolean | null
          id: string
          logo: string | null
          longueur_code: number | null
          nom: string
          nom_pays: string | null
          pays_id: string | null
          type: string
        }
        Insert: {
          actif?: boolean | null
          id?: string
          logo?: string | null
          longueur_code?: number | null
          nom: string
          nom_pays?: string | null
          pays_id?: string | null
          type: string
        }
        Update: {
          actif?: boolean | null
          id?: string
          logo?: string | null
          longueur_code?: number | null
          nom?: string
          nom_pays?: string | null
          pays_id?: string | null
          type?: string
        }
        Relationships: [
          {
            foreignKeyName: "operateurs_pays_id_fkey"
            columns: ["pays_id"]
            isOneToOne: false
            referencedRelation: "pays"
            referencedColumns: ["id"]
          },
        ]
      }
      pays: {
        Row: {
          code: string
          id: string
          is_api: boolean | null
          nom: string
          type_api: string | null
        }
        Insert: {
          code: string
          id?: string
          is_api?: boolean | null
          nom: string
          type_api?: string | null
        }
        Update: {
          code?: string
          id?: string
          is_api?: boolean | null
          nom?: string
          type_api?: string | null
        }
        Relationships: []
      }
      produits: {
        Row: {
          categorie: string | null
          description: string | null
          gencode: boolean | null
          id: string
          instruction: string | null
          is_product_new: boolean | null
          is_visible: boolean | null
          nom: string
          operateur_id: string | null
          prix_achat: number
          prix_vente: number
          type: string | null
        }
        Insert: {
          categorie?: string | null
          description?: string | null
          gencode?: boolean | null
          id?: string
          instruction?: string | null
          is_product_new?: boolean | null
          is_visible?: boolean | null
          nom: string
          operateur_id?: string | null
          prix_achat: number
          prix_vente: number
          type?: string | null
        }
        Update: {
          categorie?: string | null
          description?: string | null
          gencode?: boolean | null
          id?: string
          instruction?: string | null
          is_product_new?: boolean | null
          is_visible?: boolean | null
          nom?: string
          operateur_id?: string | null
          prix_achat?: number
          prix_vente?: number
          type?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "produits_operateur_id_fkey"
            columns: ["operateur_id"]
            isOneToOne: false
            referencedRelation: "operateurs"
            referencedColumns: ["id"]
          },
        ]
      }
      recharges: {
        Row: {
          articles: Json | null
          boutique_id: string | null
          frais: number | null
          frais_boutique: number | null
          id: string
          internal_ref: string
          montant: number
          process_state: string
          product_informations: Json | null
          qty: number | null
          sale_date: string | null
          sale_ref: string
          tva: number | null
          voucher: Json | null
        }
        Insert: {
          articles?: Json | null
          boutique_id?: string | null
          frais?: number | null
          frais_boutique?: number | null
          id?: string
          internal_ref: string
          montant: number
          process_state: string
          product_informations?: Json | null
          qty?: number | null
          sale_date?: string | null
          sale_ref: string
          tva?: number | null
          voucher?: Json | null
        }
        Update: {
          articles?: Json | null
          boutique_id?: string | null
          frais?: number | null
          frais_boutique?: number | null
          id?: string
          internal_ref?: string
          montant?: number
          process_state?: string
          product_informations?: Json | null
          qty?: number | null
          sale_date?: string | null
          sale_ref?: string
          tva?: number | null
          voucher?: Json | null
        }
        Relationships: [
          {
            foreignKeyName: "recharges_boutique_id_fkey"
            columns: ["boutique_id"]
            isOneToOne: false
            referencedRelation: "boutiques"
            referencedColumns: ["id"]
          },
        ]
      }
      recharges_flexi: {
        Row: {
          date: string | null
          frais: number | null
          frais_bout: number | null
          id: string
          is_valid: boolean | null
          montant: number
          nom_offre: string
          numero: string
          operateur_id: string | null
          user_id: string | null
        }
        Insert: {
          date?: string | null
          frais?: number | null
          frais_bout?: number | null
          id?: string
          is_valid?: boolean | null
          montant: number
          nom_offre: string
          numero: string
          operateur_id?: string | null
          user_id?: string | null
        }
        Update: {
          date?: string | null
          frais?: number | null
          frais_bout?: number | null
          id?: string
          is_valid?: boolean | null
          montant?: number
          nom_offre?: string
          numero?: string
          operateur_id?: string | null
          user_id?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "recharges_flexi_operateur_id_fkey"
            columns: ["operateur_id"]
            isOneToOne: false
            referencedRelation: "operateurs"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "recharges_flexi_user_id_fkey"
            columns: ["user_id"]
            isOneToOne: false
            referencedRelation: "users"
            referencedColumns: ["id"]
          },
        ]
      }
      types_offres: {
        Row: {
          description: string | null
          id: string
          nom: string
        }
        Insert: {
          description?: string | null
          id?: string
          nom: string
        }
        Update: {
          description?: string | null
          id?: string
          nom?: string
        }
        Relationships: []
      }
      users: {
        Row: {
          created_at: string | null
          email: string
          id: string
          nom: string
          password_hash: string | null
          picture: string | null
          prenom: string | null
          roles: string[]
          session_token: string | null
          updated_at: string | null
        }
        Insert: {
          created_at?: string | null
          email: string
          id?: string
          nom: string
          password_hash?: string | null
          picture?: string | null
          prenom?: string | null
          roles?: string[]
          session_token?: string | null
          updated_at?: string | null
        }
        Update: {
          created_at?: string | null
          email?: string
          id?: string
          nom?: string
          password_hash?: string | null
          picture?: string | null
          prenom?: string | null
          roles?: string[]
          session_token?: string | null
          updated_at?: string | null
        }
        Relationships: []
      }
    }
    Views: {
      [_ in never]: never
    }
    Functions: {
      get_current_user_role: {
        Args: Record<PropertyKey, never>
        Returns: string[]
      }
    }
    Enums: {
      [_ in never]: never
    }
    CompositeTypes: {
      [_ in never]: never
    }
  }
}

type PublicSchema = Database[Extract<keyof Database, "public">]

export type Tables<
  PublicTableNameOrOptions extends
    | keyof (PublicSchema["Tables"] & PublicSchema["Views"])
    | { schema: keyof Database },
  TableName extends PublicTableNameOrOptions extends { schema: keyof Database }
    ? keyof (Database[PublicTableNameOrOptions["schema"]]["Tables"] &
        Database[PublicTableNameOrOptions["schema"]]["Views"])
    : never = never,
> = PublicTableNameOrOptions extends { schema: keyof Database }
  ? (Database[PublicTableNameOrOptions["schema"]]["Tables"] &
      Database[PublicTableNameOrOptions["schema"]]["Views"])[TableName] extends {
      Row: infer R
    }
    ? R
    : never
  : PublicTableNameOrOptions extends keyof (PublicSchema["Tables"] &
        PublicSchema["Views"])
    ? (PublicSchema["Tables"] &
        PublicSchema["Views"])[PublicTableNameOrOptions] extends {
        Row: infer R
      }
      ? R
      : never
    : never

export type TablesInsert<
  PublicTableNameOrOptions extends
    | keyof PublicSchema["Tables"]
    | { schema: keyof Database },
  TableName extends PublicTableNameOrOptions extends { schema: keyof Database }
    ? keyof Database[PublicTableNameOrOptions["schema"]]["Tables"]
    : never = never,
> = PublicTableNameOrOptions extends { schema: keyof Database }
  ? Database[PublicTableNameOrOptions["schema"]]["Tables"][TableName] extends {
      Insert: infer I
    }
    ? I
    : never
  : PublicTableNameOrOptions extends keyof PublicSchema["Tables"]
    ? PublicSchema["Tables"][PublicTableNameOrOptions] extends {
        Insert: infer I
      }
      ? I
      : never
    : never

export type TablesUpdate<
  PublicTableNameOrOptions extends
    | keyof PublicSchema["Tables"]
    | { schema: keyof Database },
  TableName extends PublicTableNameOrOptions extends { schema: keyof Database }
    ? keyof Database[PublicTableNameOrOptions["schema"]]["Tables"]
    : never = never,
> = PublicTableNameOrOptions extends { schema: keyof Database }
  ? Database[PublicTableNameOrOptions["schema"]]["Tables"][TableName] extends {
      Update: infer U
    }
    ? U
    : never
  : PublicTableNameOrOptions extends keyof PublicSchema["Tables"]
    ? PublicSchema["Tables"][PublicTableNameOrOptions] extends {
        Update: infer U
      }
      ? U
      : never
    : never

export type Enums<
  PublicEnumNameOrOptions extends
    | keyof PublicSchema["Enums"]
    | { schema: keyof Database },
  EnumName extends PublicEnumNameOrOptions extends { schema: keyof Database }
    ? keyof Database[PublicEnumNameOrOptions["schema"]]["Enums"]
    : never = never,
> = PublicEnumNameOrOptions extends { schema: keyof Database }
  ? Database[PublicEnumNameOrOptions["schema"]]["Enums"][EnumName]
  : PublicEnumNameOrOptions extends keyof PublicSchema["Enums"]
    ? PublicSchema["Enums"][PublicEnumNameOrOptions]
    : never

export type CompositeTypes<
  PublicCompositeTypeNameOrOptions extends
    | keyof PublicSchema["CompositeTypes"]
    | { schema: keyof Database },
  CompositeTypeName extends PublicCompositeTypeNameOrOptions extends {
    schema: keyof Database
  }
    ? keyof Database[PublicCompositeTypeNameOrOptions["schema"]]["CompositeTypes"]
    : never = never,
> = PublicCompositeTypeNameOrOptions extends { schema: keyof Database }
  ? Database[PublicCompositeTypeNameOrOptions["schema"]]["CompositeTypes"][CompositeTypeName]
  : PublicCompositeTypeNameOrOptions extends keyof PublicSchema["CompositeTypes"]
    ? PublicSchema["CompositeTypes"][PublicCompositeTypeNameOrOptions]
    : never
